<?php


namespace Api\controllers;
use Api\helpers\ErrorLog;
use Api\helpers\HttpResponses;
use Api\helpers\Validations;
use Api\models\BookModel;
use Dompdf\Dompdf;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();



class BookController
{
    private $image;
    public function getAllBooks()
    {
        $page = (int) @$_GET['page'] ?? 1;
        $limit = (int) @$_GET['limit'] ?? 5;
        $search = @$_GET['search'] ?? '';
        if ($page <= 0) {
            $page = 1;
        }
        if ($limit <= 0) {
            $limit = 5;
        }
        if ($limit > 100) {
            $limit = 100;
        }
        try {
            $book_instance = new BookModel();
            $all_books = $book_instance->getAllBooks($page, $limit, $search);
            echo json_encode($all_books);
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function create($new_book, $coverImage = null)
    {
        $validationResult = Validations::validate($new_book);
        if (!$validationResult) {
            return;
        }
        try {
            $book_instance = new BookModel();
            $newImage = $coverImage ? $this->createCoverImg($coverImage) : null;
            $allData = [
                "title" => $new_book["title"],
                "author" => $new_book["author"],
                "year" => $new_book["year"],
                "genre" => $new_book["genre"],
                "coverImage" => $newImage,
                "isFavorite" => $new_book["isFavorite"],
                "user_id" => $new_book["user_id"],
                "createdAt" => date('Y-m-d H:i:s'),
                "updatedAt" => null
            ];
            $book_instance->create($allData);
            echo json_encode(HttpResponses::created());
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function uploadCoverImg($id, $coverImage)
    {
        try {
            //primero se crea la instancia
            $book_instance = new BookModel();

            //se obtiene la imagen actual de la db para actualizarlo una vez se agrega la nueva imagen si se esta editando
            $currentImage = $book_instance->getBook($id["bookId"]);

            // se mueve la imagen al servidor local en caso de sea falso por un error se detiene la ejecucion de la funcion para validar el error
            $newImage = $coverImage ? $this->createCoverImg($coverImage, $currentImage["coverImage"]) : null;

            if (!$newImage) {
                return;
            }

            ///se crea el array
            $allData = [
                "id" => $id["bookId"],
                "coverImage" => $newImage
            ];

            $book_instance->partialUpdate($allData);

            echo json_encode(HttpResponses::created());
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function deleteCoverImg($id) 
    {
        try {
            //primero se crea la instancia
            $book_instance = new BookModel();

            //se obtiene la imagen actual de la db para actualizarla a NULL
            $currentImage = $book_instance->getBook($id);

            // se elimina la imagen fisicamente
            $defaultImage = $this->deleteCoverImgFromDirectory( $currentImage["coverImage"]);

            ///se crea el array
            $allData = [
                "id" => $id,
                "coverImage" => $defaultImage
            ];

            $book_instance->partialUpdate($allData);

            echo json_encode(HttpResponses::created());
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function deleteCoverImgFromDirectory($actual_img)
    {
        $directoryOfImages = __DIR__ . '/../../public/images/';
        try {
            if (!empty($actual_img) && file_exists($directoryOfImages . $actual_img)) {
                unlink($directoryOfImages . $actual_img);
                return  null;
            }
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }


    public function createCoverImg($file, $actual_img = null)
    {
        // Crear directorio si no existe
        $directoryOfImages = __DIR__ . '/../../public/images/';
        if (!file_exists($directoryOfImages)) {
            mkdir($directoryOfImages, 0777, true);
        }

        // Eliminar la imagen anterior si existe
        if (!empty($actual_img) && file_exists($directoryOfImages . $actual_img)) {
            unlink($directoryOfImages . $actual_img);
        }

        $this->image = uniqid() . '-' . basename($file['coverImage']['name']);
        $filePath = $directoryOfImages . '/' . $this->image;
        $fileType = strtolower(pathinfo($this->image, PATHINFO_EXTENSION));

        $checkImageSize = getimagesize($file['coverImage']['tmp_name']);

        if ($checkImageSize != false) {

            $size = $file['coverImage']['size'];

            if ($size > 5000000) {
                echo json_encode(HttpResponses::notFound("The file has to be less than 5Mb"));
                return false;
            } else {
                if ($fileType == "jpg" || $fileType == "jpeg" || $fileType == "png" || $fileType == "gif") {
                    if (move_uploaded_file($file['coverImage']['tmp_name'], $filePath)) {
                        return $this->image;
                    } else {
                        echo json_encode(HttpResponses::notFound("There was an error uploading this file"));
                        return false;
                    }
                } else {
                    echo json_encode(HttpResponses::notFound("Only jpg, png and jpeg files are admitted"));
                    return false;
                }
            }

        } else {
            echo json_encode(HttpResponses::notFound("The file is not an image"));
            return false;
        }
    }

    public function generatePdf($books)
    {
        $dompdf = new Dompdf();

        $css = file_get_contents(__DIR__ . '/../../css/pdf-style.css');

        $html = "
            <html>
            <head>
                <meta charset='utf-8'>
                <style>
                    $css
                </style>
            </head>
            <body>
            <h1>Books report</h1>
            <table class='book-table'>
                <thead>
                    <tr>
                    <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>Genre</th>
                        <th>Favorite book</th>
                        <th>Date of creation</th>
                    </tr>
                    </thead>
                    <tbody>";

        foreach ($books as $book) {
            $genres = json_decode($book['genre'], true);
            $genres_string = is_array($genres) ? implode(', ', $genres) : $book['genre'];
            $is_favorite = $book['isFavorite'] == 1 ? 'Yes' : 'No';
            $date = date('d/m/Y', strtotime($book['createdAt']));
            $year = date('Y', strtotime($book['year']));
            $html .=
            "<tr>
            <td>{$book['id']}</td>
            <td>{$book['title']}</td>
            <td>{$book['author']}</td>
            <td>{$year}</td>
            <td>{$genres_string}</td>
            <td>{$is_favorite}</td>
            <td>{$date}</td>";
        }

        $html .= "</tr>
                    </tbody>
                </table>
            </body>
            </html>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("archivo_.pdf", array("Attachment" => false));
    }

    public function generateOnePdf($book, $id)
    {
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set(array('isRemoteEnabled' => true));
        $dompdf->setOptions($options);
        $current_date = date('d/m/Y');

        $css = file_get_contents(__DIR__ . '/../../css/pdf-style.css');

       
        $html = "<html>
                <head>
                    <meta charset='utf-8'>
                    <style>
                        $css
                    </style>
                </head>";

        $html .= "<body>
                    <h1 style='text-align: center;'>Book report</h1>
                    <div class='book_container' >
                    <div class='book-info'>
                        <p class='book_title'><strong>Title:</strong> {$book["title"]}</p>
                        <p class='book_title'><strong>Author:</strong> {$book["author"]}</p>
                        <p class='book_title'><strong>Year:</strong> {$book["year"]}</p>
                        <p class='book_title'><strong>Genre:</strong></p>";
                        
                        $genres = json_decode($book["genre"], true);
                        if (count($genres) === 0) {
                           $html.="<p class='info_text'>This book doesn't have any genre</p>";
                        } else {
                            $html.= "<div class='genres-list'>";
                            foreach ($genres as $genre) {
                                $html.= "<span class='genre-badge'>{$genre}</span>";
                            }
                            $html.= "</div>";
                        }
                      
                        $html .= '
                        <p class="book_title"><strong>Favorite:</strong> ' . ($book['isFavorite'] == 1 ? 'Marked as favorite' : 'It has not been marked as favorite') . '</p>
                        <p class="book_title"><strong>Date of creation:</strong> ' . $book["createdAt"] . '</p>
                        <p class="book_title"><strong>Last time it was updated:</strong> ' . ($book["updatedAt"] !== null ? $book["updatedAt"] : 'It has not been updated yet') . '</p>
                        <p class="book_title"><strong>Book cover:</strong></p>
                        <div class="bookimg-container"><img src="'. $_ENV["UPLOADED_IMG_PATH"] .'/public/images/' . ($book['coverImage'] ? $book['coverImage'] : "Image_not_available.png") . '" class="book-cover"/></div>
                    </div>
                    </div>
                    <h1 class="footer-info">This document was printed on '. $current_date .' | Book ID: '. $id .'</h1>
                    </body></html>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("book_{$id}.pdf", array("Attachment" => false));
    }

    public function getBook(int $id)
    {
        try {
            $book_instance = new BookModel();
            $book = $book_instance->getBook($id);
            if (!empty($book)) {
                echo json_encode($book);
                return;
            } else {
                echo json_encode(HttpResponses::notFound("The book with ID $id does not exist!"));
            }
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function update(int $id, array $data)
    {
        $validationResult = Validations::validate($data);
        if (!$validationResult) {
            return;
        }
        try {
            $book_instance = new BookModel();
            $allData = [
                "id" => $id,
                "title" => $data["title"],
                "author" => $data["author"],
                "year" => $data["year"],
                "genre" => $data["genre"],
                "isFavorite" => $data["isFavorite"],
                "user_id" => $data["user_id"],
                "updatedAt" => date('Y-m-d H:i:s')
            ];

            $book_instance->update($allData);
            echo json_encode(HttpResponses::ok("Book with id " . $id . " has been updated."));
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function partialUpdate(int $id, $dataToPatch)
    {
        try {
            $allData = array_merge(["id" => $id], $dataToPatch);
            $book_instance = new BookModel();
            $book_instance->partialUpdate($allData);
            echo json_encode(HttpResponses::ok("Book with id " . $id . " has been updated."));
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

    public function delete(int $id)
    {
        try {
            $book_instance = new BookModel();
            $book_instance->delete($id);
            echo json_encode(HttpResponses::noContent());
        } catch (\Throwable $error) {
            echo json_encode(HttpResponses::serverError());
            ErrorLog::showErrors();
            error_log("Error message \n" . $error);
        }
    }

}