<?php
$error = "";
$englishWords = "";
$khmerWords = "";
$usd = "";
$amount = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST["amount"];

    if (!is_numeric($amount) || $amount <= 0) {
        $error = "សូមបញ្ចូលតម្លៃជាលេខត្រឹមត្រូវ។";
    } else {
        $englishWords = convertToWords((int)$amount);
        $khmerWords = convertToKhmerWords((int)$amount);
        $usd = number_format($amount / 4000, 2) . " $";

        $log = "Riel: $amount | English: $englishWords | Khmer: $khmerWords | USD: $usd" . PHP_EOL;
        file_put_contents("log.txt", $log, FILE_APPEND);
    }
}

function convertToWords($num) {
    $ones = ["", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];
    $teens = ["ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"];
    $tens = ["", "", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"];
    $words = "";

    if ($num == 0) return "zero";
     if ($num >= 10000000) {
        $words .= $ones[intval($num / 10000000)] . "one million ";
        $num %= 10000000;
     }
    if ($num >= 1000000) {
        $words .= $ones[intval($num / 1000000)] . " million ";
        $num %= 1000000;
    }
    if ($num >= 1000) {
        $words .= convertToWords(intval($num / 1000)) . " thousand ";
        $num %= 1000;
    }
    if ($num >= 100) {
        $words .= $ones[intval($num / 100)] . " hundred ";
        $num %= 100;
    }
    if ($num >= 20) {
        $words .= $tens[intval($num / 10)] . " ";
        $num %= 10;
    } elseif ($num >= 10) {
        $words .= $teens[$num - 10] . " ";
        $num = 0;
    }
    if ($num > 0) {
        $words .= $ones[$num] . " ";
    }
    return ucfirst(trim($words)) . " riel";
}

function convertToKhmerWords($number) {
    $khmerDigits = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
    $khmerWords = ['', 'មួយ', 'ពីរ', 'បី', 'បួន', 'ប្រាំ', 'ប្រាំមួយ', 'ប្រាំពីរ', 'ប្រាំបី', 'ប្រាំបួន'];
    $result = "";
    $numStr = strval($number);
    $len = strlen($numStr);
    $positions = ["រាយ", "ដប់", "រយ", "ពាន់", "ម៉ឺន", "សែន", "លាន","មួយលាន"];

    for ($i = 0; $i < $len; $i++) {
        $digit = intval($numStr[$i]);
        $pos = $len - $i - 1;
        if ($digit > 0) {
            $result .= $khmerWords[$digit] . $positions[$pos] . " ";
        }
    }
    return trim($result) . " រៀល";
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>PHP Currency Converter</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Khmer', sans-serif;
            background: linear-gradient(to right, #f7f9fc, #e2ecf9);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card {
            background: white;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
            transform: scale(1.02);
        }

        .loader {
            display: none;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 10px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .result {
            text-align: left;
            margin-top: 20px;
            background-color: #f0f8ff;
            padding: 15px;
            border-radius: 8px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        @media screen and (max-width: 500px) {
            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>បញ្ចូលចំនួនរៀល</h2>
        <form method="post" onsubmit="showLoader()">
            <input type="text" name="amount" placeholder="សូមបញ្ចូលចំនួន..." value="<?php echo htmlspecialchars($amount); ?>">
            <input type="submit" value="បំលែង">
            <div class="loader" id="loader"></div>
        </form>

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif ($englishWords): ?>
            <div class="result">
                <p><strong>អក្សរអង់គ្លេស:</strong> <?php echo $englishWords; ?></p>
                <p><strong>អក្សរខ្មែរ:</strong> <?php echo $khmerWords; ?></p>
                <p><strong>ដុល្លារ:</strong> <?php echo $usd; ?></p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function showLoader() {
            document.getElementById('loader').style.display = 'block';
        }
    </script>
</body>
</html>
