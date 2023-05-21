<!DOCTYPE html>
<html>
<head>
  <title>Form Submission Results</title>
</head>
<body>
  <h1>Form Submission Results</h1>

  <table>
    <thead>
      <tr>
        <th>Type</th>
        <th>Weapon</th>
        <th>Armor</th>
        <th>Size</th>
        <th>Training</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $types = $_POST['type'];
        $weapons = $_POST['weapon'];
        $armors = $_POST['armor'];
        $sizes = $_POST['size'];
        $trainings = $_POST['training'];

        // Loop through the submitted values and display them
        for ($i = 0; $i < count($types); $i++) {
          $type = $types[$i];
          $weapon = $weapons[$i];
          $armor = $armors[$i];
          $size = $sizes[$i];
          $training = $trainings[$i];

          echo "<tr>";
          echo "<td>$type</td>";
          echo "<td>$weapon</td>";
          echo "<td>$armor</td>";
          echo "<td>$size</td>";
          echo "<td>$training</td>";
          echo "</tr>";
        }
      }
      ?>
    </tbody>
  </table>
</body>
</html>