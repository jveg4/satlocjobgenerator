<!DOCTYPE html>
<html>

<head>
    <title>Satloc JOB Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        #header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        #formSection {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            margin-bottom: 5px;
            font-weight: 500;
        }

        input[type="file"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        #progress,
        #result {
            margin-top: 20px;
            display: none;
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="header">
        <h1>Satloc JOB Generator</h1>
    </div>

    <div id="formSection" class="container">
        <form id="uploadForm" enctype="multipart/form-data">
            <label>Select KML File:</label>
            <input type="file" name="kmlFile" class="form-control" required>
            <label for="jobNumber">Select Job Number:</label>
            <select name="jobNumber" id="jobNumber" class="form-control">
                <?php
                for ($i = 1; $i <= 999; $i++) {
                    echo "<option value='$i'>$i</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn btn-primary">Upload and Process</button>
          
        </form>

        <div id="progress" style="display: none;">Processing...</div>
        <div id="result" style="display: none;"></div>
    </div>
    <hr>
<center><a href="clean.php"> Clean Old Files </a></center>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function (event) {
            event.preventDefault();
            document.getElementById('progress').style.display = 'block';

            let formData = new FormData(this);
            fetch('process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('progress').style.display = 'none';
                    document.getElementById('result').style.display = 'block';
                    document.getElementById('result').innerHTML = data;

                    // Clear the form after processing
                    document.getElementById('uploadForm').reset();
                })
                .catch(error => {
                    document.getElementById('progress').style.display = 'none';
                    document.getElementById('result').style.display = 'block';
                    document.getElementById('result').textContent = 'An error occurred: ' + error.message;

                    // Clear the form after error
                    document.getElementById('uploadForm').reset();
                });
        });
    </script>
</body>

</html>
