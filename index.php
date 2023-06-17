<?php
function isHidden($file)
{
    return strpos($file, '.') === 0;
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Explorer</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .folder-icon {
            width: 50%;
            max-width: 150px;
            max-height: 150px;
            text-align: center;
        }

        .folder-name {
            font-size: 2rem;
            text-align: center;
        }

        @media (min-width: 992px) {
            .folder-name {
                font-size: 1.3rem;
                text-align: center;
            }
        }

        .file-section {
            margin-top: 30px;
            font-size: 1.7rem;
        }

        .file-table th {
            cursor: pointer;
        }

        .container {
            margin-top: 50px;
        }

        .folder-section-c {
            text-align: center;
            margin-top: 20px;
        }

        @media (min-width: 1200px) {
            .container {
                max-width: 95%;
            }

            .folder-section-c {
                margin-top: 20px;
            }
        }
        .navbar-expand-lg .navbar-nav .nav-link {

    font-size: 1.5rem;
    font-weight: 600;
    color:#737373;
    text-decoration: underline;
    
}
li {
    box-sizing: border-box;
    border-right: 2px;
    border-right-style: double;
}
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="./"><h1>Home</h1></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
   
        <li class="nav-item">
          <a class="nav-link" href="./phpmyadmin">Php My Admin</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Quick Link 1 </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Quick Link 2 </a>
        </li>
     
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <h2>Folder Explorer</h2>
            <div class="folder-section">
                <div class="row">
                    <?php
                    $classr = '';
                    $directory = './'; // Replace with the actual directory path

                    $folders = array_diff(scandir($directory), ['.', '..']);

                    foreach ($folders as $folder):
                        $folderPath = $directory . '/' . $folder;

                        if (is_dir($folderPath) && !isHidden($folder)):
                            ?>
                            <div class="folder-section-c col-md-4 col-lg-3 col-sm-2 col-xs-5">
                                <a href="<?php echo $folderPath; ?>">
                                    <img src="icon.png" alt="<?php echo $folder; ?>" class="folder-icon">
                                </a>
                                <div class="folder-name"><?php echo $folder; ?></div>
                            </div>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="file-section">
                <h2>Other Files</h2>
                <table class="table file-table">
                    <thead>
                    <tr>
                        <th onclick="sortFiles('name')">Name</th>
                        <th onclick="sortFiles('type')">Type</th>
                        <th onclick="sortFiles('size')">Size</th>
                        <th onclick="sortFiles('date')">Date</th>
                        <th>Download</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $files = array_diff(scandir($directory), ['.', '..']);
                    $fileData = [];

                    foreach ($files as $file):
                        $filePath = $directory . '/' . $file;

                        if (!is_dir($filePath) && !isHidden($file)) {
                            $fileType = mime_content_type($filePath);
                            $fileSize = filesize($filePath);
                            $fileDate = date('Y-m-d', filemtime($filePath));

                            $fileData[] = [
                                'name' => $file,
                                'type' => $fileType,
                                'size' => $fileSize,
                                'date' => $fileDate
                            ];
                        }
                    endforeach;

                    // Sort the file data based on the selected field
                    $sortField = isset($_GET['sort']) ? $_GET['sort'] : 'name';
                    $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';

                    if ($sortOrder === 'asc') {
                        usort($fileData, function ($a, $b) use ($sortField) {
                            return $a[$sortField] <=> $b[$sortField];
                        });
                    } else {
                        usort($fileData, function ($a, $b) use ($sortField) {
                            return $b[$sortField] <=> $a[$sortField];
                        });
                    }

                    foreach ($fileData as $file):
                        $filePath = $directory  . $file['name'];
                        ?>
                        <tr>
                            <td><a href="<?php echo $filePath; ?>"><?php echo $file['name']; ?></a></td>
                            <td><?php echo $file['type']; ?></td>
                            <td><?php echo formatSizeUnits($file['size']); ?></td>
                            <td><?php echo $file['date']; ?></td>
                            <td><a href="<?php echo $filePath; ?>" class="btn btn-primary" download>Download</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function sortFiles(field) {
        const currentUrl = new URL(window.location.href);
        const sortOrder = currentUrl.searchParams.get('order') || 'asc';

        if (currentUrl.searchParams.get('sort') === field) {
            currentUrl.searchParams.set('order', sortOrder === 'asc' ? 'desc' : 'asc');
        } else {
            currentUrl.searchParams.set('sort', field);
            currentUrl.searchParams.set('order', 'asc');
        }

        window.location.href = currentUrl.toString();
    }
</script>
</body>
</html>
