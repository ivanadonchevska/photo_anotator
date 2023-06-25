<!DOCTYPE html>
<html>
<head>
    <title>Photo Uploader</title>
</head>
<body>
    <h1>Photo Uploader</h1>

    <!-- Display uploaded photos with annotations -->
    <div id="photo-container">
        <?php
        // Configuration for database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "db_photo";

        // Create a connection to the database
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Retrieve the uploaded photos from the database
        $sql = "SELECT * FROM photos";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $photoId = $row["id"];
                $photoPath = $row["path"];

                // Retrieve annotations for the current photo
                $annotationSql = "SELECT * FROM annotations WHERE photo_id = $photoId";
                $annotationResult = $conn->query($annotationSql);

                // Display the photo and annotations
                echo '<div>';
                echo '<img src="' . $photoPath . '" alt="Uploaded Photo" style="max-width: 500px;"><br>';

                if ($annotationResult->num_rows > 0) {
                    echo '<ul>';
                    while ($annotationRow = $annotationResult->fetch_assoc()) {
                        $coordinates = $annotationRow["coordinates"];
                        $additionalData = $annotationRow["additional_column"];
                        // Display the annotations as needed
                        echo '<li>Coordinates: ' . $coordinates . ', Additional Data: ' . $additionalData . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No annotations available for this photo.</p>';
                }

                // Add the delete button
                echo '<form class="delete-form" method="POST">';
                echo '<input type="hidden" name="photo_id" value="' . $photoId . '">';
                echo '<input type="button" value="Delete" onclick="deletePhoto(' . $photoId . ')">';
                echo '</form>';
                
                // Add the add annotation form
                echo '<form class="add-annotation-form" method="POST">';
                echo '<input type="hidden" name="photo_id" value="' . $photoId . '">';
                echo '<input type="text" name="coordinates" placeholder="Coordinates" required>';
                echo '<input type="text" name="additional_data" placeholder="Additional Data" required>';
                echo '<input type="submit" value="Add Annotation">';
                echo '</form>';

                echo '</div>';
            }
        } else {
            echo '<p>No uploaded photos found.</p>';
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <!-- Upload form -->
    <form id="upload-form" enctype="multipart/form-data">
        <input type="file" name="photo" accept="image/*" required>
        <input type="submit" value="Upload">
    </form>

    <!-- JavaScript code -->
    <script>
        // Handle the form submission
        document.getElementById("upload-form").addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent the form from submitting

            var formData = new FormData(this);

            fetch("upload.php", {
                method: "POST",
                body: formData,
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.status === "success") {
                        // Reload the page to display the new photo
                        location.reload();
                    } else {
                        console.log(data.message);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        });

        

    // Handle the form submission for deleting a photo
    function deletePhoto(photoId) {
        if (confirm("Are you sure you want to delete this photo?")) {
            fetch("delete.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "photo_id=" + encodeURIComponent(photoId),
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.status === "success") {
                        // Remove the deleted photo from the page
                        var photoDiv = document.querySelector('input[name="photo_id"][value="' + photoId + '"]').parentNode;
                        photoDiv.parentNode.removeChild(photoDiv);
                        // Reload the page to reflect the changes
                        location.reload();
                    } else {
                        console.log(data.message);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }


        // Handle the form submission for adding an annotation
        var addAnnotationForms = document.querySelectorAll(".add-annotation-form");
        addAnnotationForms.forEach(function (form) {
            form.addEventListener("submit", function (e) {
                e.preventDefault(); // Prevent the form from submitting

                var formData = new FormData(this);
                var photoId = this.querySelector('input[name="photo_id"]').value;

                fetch("add_annotation.php", {
                    method: "POST",
                    body: formData,
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.status === "success") {
                            // Reload the page to display the updated annotation
                            location.reload();
                        } else {
                            console.log(data.message);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
        });
    </script>
</body>
</html>
