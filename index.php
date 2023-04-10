<!DOCTYPE html>
<html>

<head>
    <title>Simple XSS Test</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
    <?php
    // Suppress error output
    error_reporting(0);
    ini_set('display_errors', 0);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
        $content = $_POST['content'];
        if (!empty($content)) {
            $filename = 'messages.txt';
            $handle = fopen($filename, 'a');
            fwrite($handle, $content . "\n");
            fclose($handle);
        }
        header('Location: index.php');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filename = $_FILES['file']['name'];
        $tempname = $_FILES['file']['tmp_name'];
        $destination = 'uploads/' . $filename;
        move_uploaded_file($tempname, $destination);
        header('Location: index.php');
    }
    if (isset($_POST['reset'])) {
        $filename = 'messages.txt';
        if (file_exists($filename)) {
            unlink($filename);
        }
        $default_content = '';
        $handle = fopen($filename, 'w');
        fwrite($handle, $default_content);
        fclose($handle);
        header('Location: index.php');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-file'])) {
        $file_to_delete = $_POST['delete-file'];
        $path_to_file = 'uploads/' . $file_to_delete;
        if (is_file($path_to_file)) {
            unlink($path_to_file);
        }
        header('Location: index.php');
    }
    ?>
    <div class="container">
        <h1>Simple XSS Test</h1>
        <div class="row">
            <div class="col-sm-6">
                <form method="post">
                    <div class="form-group">
                        <label for="content">Write something:</label>
                        <textarea class="form-control" name="content"></textarea>
                    </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="col-sm-6">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Upload a file:</label>
                        <input type="file" name="file">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
        <div class="row">
            <br><br>
            <div class="col-sm-6">
                <h2>Messages:</h2>
                <form method="post" class="float-right">
                    <input type="hidden" name="reset" value="true">
                    <button type="submit" class="btn btn-danger">Reset Messages</button>
                </form>
                <hr>
                <ul>
                    <?php
                    $filename = 'messages.txt';
                    if (file_exists($filename)) {
                        $lines = file($filename);
                        foreach ($lines as $line) {
                            echo '<li>' . $line . '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <br>
            <div class="col-sm-6">
                <h2>Uploaded files:</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="delete-file">Select a file to delete:</label>
                        <select class="form-control" name="delete-file">
                            <?php
                            $dir = 'uploads';
                            if (is_dir($dir)) {
                                $files = scandir($dir);
                                foreach ($files as $file) {
                                    $path = $dir . '/' . $file;
                                    if (is_file($path)) {
                                        echo '<option value="' . $file . '">' . $file . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger">Delete file</button>
                </form>
                <hr>
                <?php
                $dir = 'uploads';
                if (is_dir($dir)) {
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        $path = $dir . '/' . $file;
                        if (is_file($path)) {
                            $mime = mime_content_type($path);
                            if (substr($mime, 0, 5) == 'image') {
                                echo '<h4>' . $file . '</h4>';
                                echo '<img src="' . $path . '" class="img-responsive" alt="' . $file . '">';
                            } else if ($mime == 'text/plain' || $mime == 'application/xml') {
                                echo '<h4>' . $file . '</h4>';
                                echo '<pre>' . file_get_contents($path) . '</pre>';
                            } else {
                                echo '<h4>' . $file . '</h4>';
                                echo '<pre>' . file_get_contents($path) . '</pre>';
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
