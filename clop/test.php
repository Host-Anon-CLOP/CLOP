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
 

      function addNewGroup() {
        var e = document.getElementById("unit-type");
        var value = e.options[e.selectedIndex].value;
        var unittype = e.options[e.selectedIndex].text;

        const groupFields = document.getElementById('group-fields');

        const groupDiv = document.createElement('div');

        const unitLabel = document.createElement('label');
        unitLabel.textContent = 'Unit: ' + unittype;
        groupDiv.appendChild(unitLabel);

        const weaponLabel = document.createElement('label');
        weaponLabel.textContent = 'Weapon:';
        groupDiv.appendChild(weaponLabel);

        const weaponSelect = document.createElement('select');
        weaponSelect.name = 'weapon';
        if (unittype == 'Cavalry') {
        weaponSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="prc-e6">PRC-E6</option>' +
          '<option value="prc-e7">PRC-E7</option>' +
          '<option value="prc-e8">PRC-E8</option>' +
          '<option value="acfu">ACFU</option>' +
          '<option value="apfu">APFU</option>' +
          '<option value="atfu">ATFU</option>' +
          '<option value="aufu">AUFU</option>';
        } if (unittype == 'Tanks') {
        weaponSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="k9p">K9P</option>' +
          '<option value="elbo-grs">ELBO-GRS</option>' +
          '<option value="chem-light">Chem-Light</option>';
        } else if (unittype == 'Pegasi') {
        weaponSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="propwash">PropWash</option>' +
          '<option value="steambucket">SteamBucket</option>' +
          '<option value="canopylights">CanopyLights</option>';
        } else if (unittype == 'Unicorns') {
        weaponSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="longstand">LongStand</option>' +
          '<option value="longweight">LongWeight</option>' +
          '<option value="gridsquares">GridSquares</option>';
        } else if (unittype == 'Navy') {
        weaponSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="shoreline">Shoreline</option>' +
          '<option value="waterhammer">WaterHammer</option>' +
          '<option value="waterlineeraser">WaterlineEraser</option>';
        }
        groupDiv.appendChild(weaponSelect);

        const armorLabel = document.createElement('label');
        armorLabel.textContent = ' Armor:';
        groupDiv.appendChild(armorLabel);

        const armorSelect = document.createElement('select');
        armorSelect.name = 'armor';
        if (unittype == 'Cavalry') {
        armorSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="barding">Barding</option>' +
          '<option value="bigdog">Bigdog</option>' +
          '<option value="nope">Nope</option>';
        } else if (unittype == 'Tanks') {
          armorSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="trundle">Trundle</option>' +
          '<option value="shepherd">Shepherd</option>' +
          '<option value="ohno">Ohno</option>' +
          '<option value="titan">Titan</option>';
        } else if (unittype == 'Pegasi') {
          armorSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="cooler">Cooler</option>' +
          '<option value="wonder">Wonder</option>' +
          '<option value="griffin">Griffin</option>' +
          '<option value="dragon">Dragon</option>';
        } else if (unittype == 'Unicorns') {
          armorSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="hornshield">Hornshield</option>' +
          '<option value="librarian">Librarian</option>' +
          '<option value="shining">Shining</option>' +
          '<option value="d2a">D2A</option>';
        } else if (unittype == 'Navy') {
          armorSelect.innerHTML = '<option value="scrounged">Scrounged</option>' +
          '<option value="c-pon3">C-PON3</option>' +
          '<option value="esohes">Esohes</option>' +
          '<option value="shubidu">Shubidu</option>';
        }
        groupDiv.appendChild(armorSelect);

        groupFields.appendChild(groupDiv);
      }
    </script>
  </form>
</body>
</html>
EOFORM;
?>