<!DOCTYPE html>
<html>
<head>
    <title>Happy Birthday!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: auto;
        }
        .content-table {
            width: 100%;
        }
        .content-table td {
            vertical-align: top;
            padding: 10px;
        }
        .image-cell {
            text-align: right;
        }
        .image-cell img {
            width: 100%;
            max-width: 200px;
        }
    </style>
</head>
<body>
<div class="container">
    <table class="content-table" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="width: 70%;">
                <p>Dear {{ $employeeName }},</p>
                <p>
                    {{ $companyName }} wishes you a Happy Birthday,<br>
                    We wish you a very happy, prosperous, and successful year ahead.
                </p>
                <p>Best Wishes,</p>
            </td>
            <td class="image-cell" style="width: 30%;">
                <img src="{{ $imageUrl }}">
            </td>
        </tr>
    </table>
</div>
</body>
</html>
