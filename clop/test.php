<?php
#include("backend/backend_warguide.php");
/*
    <label for="groups">Number of Groups:</label>
    <input type="number" name="groups" id="groups" min="1" required><br><br>
    <div id="group-fields"></div>
*/

echo <<<EOFORM
<html>
<body>
  <form method="POST" action="/calculate">
    <button type="submit">Calculate</button>

    <select name="unit-type" class="form-control" style="width:210px;"/>
      <option value="Cavalry"</option>
      <option value="Tanks"</option>
      <option value="Pegasi"</option>
      <option value="Unicorns"</option>
      <option value="Navy"</option>
      <option value="Alicorns"</option>
    </select>

    <button type="button" onclick="addNewGroup()">Add Group</button>

    <script>
      function addNewGroup() {
        const groupFields = document.getElementById('group-fields');

        const groupDiv = document.createElement('div');

        const unitLabel = document.createElement('label');
        unitLabel.textContent = 'Unit:';
        groupDiv.appendChild(unitLabel);

        const unitSelect = document.createElement('select');
        unitSelect.name = 'unit';
        unitSelect.innerHTML = '<option value="cavalry">Cavalry</option>' +
          '<option value="tanks">Tanks</option>' +
          '<option value="pegasi">Pegasi</option>' +
          '<option value="unicorns">Unicorns</option>';
        groupDiv.appendChild(unitSelect);

        const armorLabel = document.createElement('label');
        armorLabel.textContent = 'Armor:';
        groupDiv.appendChild(armorLabel);

        const armorSelect = document.createElement('select');
        armorSelect.name = 'armor';
        armorSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="custom">Custom</option>' +
          '<option value="standard">Standard</option>';
        groupDiv.appendChild(armorSelect);

        const weaponLabel = document.createElement('label');
        weaponLabel.textContent = 'Weapon:';
        groupDiv.appendChild(weaponLabel);

        const weaponSelect = document.createElement('select');
        weaponSelect.name = 'weapon';
        weaponSelect.innerHTML = '<option value="canopy lights">Canopy Lights</option>' +
          '<option value="scrounged">Scrounged</option>' +
          '<option value="standard">Standard</option>';
        groupDiv.appendChild(weaponSelect);

        groupFields.appendChild(groupDiv);
      }
    </script>
  </form>
</body>
</html>
EOFORM;
?>