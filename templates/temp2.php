<?php
// Handle dynamic profile image URL
$imageUrl = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : 'https://via.placeholder.com/150';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelly White - Art Director Resume</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            width: 800px;
            margin: 0 auto;
            display: flex;
            background-color: #000;
            color: #fff;
        }
        .left-section {
            flex: 2;
            padding: 20px;
        }
        .right-section {
            flex: 1;
            background-color: #333;
            padding: 20px;
            text-align: center;
        }
        .header {
            background-color: #f4a261;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 5px solid #000;
        }
        .profile-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="header">
                <h1>KELLY WHITE</h1>
                <h2>ART DIRECTOR</h2>
            </div>
            <div class="section-title">EDUCATION</div>
            <div class="section-content">
                <ul>
                    <?php
                    $education = [
                        ["year" => "2010 - 2015", "description" => "Bachelor of Arts in Graphic Design - XYZ University"],
                        ["year" => "2015 - 2020", "description" => "Master's in Visual Communication - ABC Institute"]
                    ];
                    foreach ($education as $edu) {
                        echo "<li><span style='background-color: #f4a261; padding: 5px; color: #fff;'>{$edu['year']}</span> {$edu['description']}</li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="section-title">EXPERIENCE</div>
            <div class="section-content">
                <ul>
                    <?php
                    $experience = [
                        ["year" => "2015 - Present", "description" => "Senior Art Director - ABC Agency"],
                        ["year" => "2010 - 2015", "description" => "Graphic Designer - XYZ Studio"]
                    ];
                    foreach ($experience as $exp) {
                        echo "<li><span style='background-color: #f4a261; padding: 5px; color: #fff;'>{$exp['year']}</span> {$exp['description']}</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="right-section">
            <div class="profile-img">
                <img src="<?php echo $imageUrl; ?>" alt="Profile Picture">
            </div>
            <div class="contact-box">CONTACT ME</div>
            <p><span style="color: #f4a261;">&#128205;</span> ADDRESS: 123, Street Ocalaho, NYC, USA</p>
            <p><span style="color: #f4a261;">&#127760;</span> WEB: contactme@email.com</p>
            <p><span style="color: #f4a261;">&#128222;</span> PHONE: 0123 0000 8000 0000</p>
        </div>
    </div>
</body>
</html>
