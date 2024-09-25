<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: left;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
        }
        .content {
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .highlight {
            color: red;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="logo_url_here" alt="Crizac Logo">
            <h2>Dear Abhinav Jain</h2>
        </div>
        <div class="content">
            <p>Greetings from Crizac Ltd!</p>
            <p>The application of the below student which you have submitted has been updated with following Status.</p>
            <table class="table">
                <tr>
                    <th>CAMS ID</th>
                    <th>Student Name</th>
                    <th>University Name</th>
                    <th>Course Applied</th>
                </tr>
                <tr>
                    <td>746414</td>
                    <td>SAIRAM PATHI</td>
                    <td>University of Greenwich</td>
                    <td>Marketing Management, MBA</td>
                </tr>
            </table>
            <table class="table">
                <tr>
                    <th>Application New Status</th>
                    <th>Comment for this Status</th>
                </tr>
                <tr>
                    <td>Offer Issued - Conditional Offer</td>
                    <td>Dear Team, <br> Kindly update the payment status of the student. <br> Regards</td>
                </tr>
            </table>
            <p>You can login to your portal for further action.</p>
            <p class="highlight">Please note that for us to serve you better please do not reply to this mail. Any communication for this application please make comment to student comment by login to your application portal <a href="https://www.crizac.com/login">www.crizac.com/login</a>. This will help us serve you better. We look forward to convert this student.</p>
        </div>
        <div class="footer">
            <p>Kind regards</p>
            <p>Crizac Limited</p>
            <p><strong>Notice of Confidentiality</strong></p>
            <p>This e-mail (and its attachment(s) if any) is intended for the named addressee(s) only. It contains information which may be confidential and which may also be privileged. Unless you are the named addressee (or authorised to receive it for the addressee) you may not read, copy or use it, or disclose it to anyone else. Unauthorised use, copying or disclosure is strictly prohibited and may be unlawful. If you have received this transmission in error please contact the sender.</p>
        </div>
    </div>
</body>
</html>
