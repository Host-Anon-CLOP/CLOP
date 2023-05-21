<?php
/*
    <label for="groups">Number of Groups:</label>
    <input type="number" name="groups" id="groups" min="1" required><br><br>
    
            const unitSelect = document.createElement('select');
        unitSelect.name = 'unit';
        unitSelect.innerHTML = '<option value="cavalry">Cavalry</option>' +
          '<option value="tanks">Tanks</option>' +
          '<option value="pegasi">Pegasi</option>' +
          '<option value="unicorns">Unicorns</option>';
        groupDiv.appendChild(unitSelect);

*/

echo <<<EOFORM
<html>
<body>
  <form method="post" action="test.php">
    <div id="group-fields"></div>

    <button type="button" onclick="calculate()">Calculate</button>

    <select name="unit-type" id="unit-type" class="form-control" style="width:210px;"/>
      <option value="Cavalry">Cavalry</option>
      <option value="Tanks">Tanks</option>
      <option value="Pegasi">Pegasi</option>
      <option value="Unicorns">Unicorns</option>
      <option value="Navy">Navy</option>
      <option value="Alicorns">Alicorns</option>
    </select>

    <button type="button" onclick="addNewGroup()">Add Group</button>

    <script>
      function calculate() {
      var element = document.getElementById('group-fields');
      var children = element.children;
      for(var i=0; i<children.length; i++){
          var child = children[i];
          echo "$child.outerHTML";
        }
      }

 
    </script>
  </form>
</body>
</html>
EOFORM;
?>