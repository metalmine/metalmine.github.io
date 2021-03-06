<?php
$page['title'] = '';
$page['meta'] = '';
$page['scripts'] = [
    '/Charts/Chart.js',
];
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/db.php";
?>
<script>
    var jsonData = []
    jsonData['diff'] = <?php include $_SERVER['DOCUMENT_ROOT'] . '/sql/monsters-difficulty-list-get.php'?>;
    jsonData['type'] = <?php include $_SERVER['DOCUMENT_ROOT'] . '/sql/weapons-type-list-get.php'?>;
    jsonData['runs_weapons_maps_monsters'] = <?php include $_SERVER['DOCUMENT_ROOT'] . '/sql/runs_weapons_maps_monsters-get.php'?>;
    // TODO: ^^translation to charts^^
    // TODO: move to seperate file
    function fillSelect(nValue, nList) {
		nList.options.length = 1
		let curr = jsonData['diff'][nValue]
		for (let key in curr) {
            if (curr.hasOwnProperty(key)) {
                let nOption = document.createElement('option')
                nOption.appendChild(document.createTextNode(curr[key].name))
                nOption.setAttribute("value", curr[key].name)
                nList.appendChild(nOption)
			}
        }
	}
    // TODO: move to seperate file
    function typeSelect(nValue, nList) {
        nList.options.length = 1
        let curr = jsonData['type'][nValue]
        for (let key in curr) {
            if (curr.hasOwnProperty(key)) {
                let nOption = document.createElement('option')
                nOption.appendChild(document.createTextNode(curr[key].weaponName))
                nOption.setAttribute("value", curr[key].weaponName)
                nList.appendChild(nOption)
            }
        }
    }

</script>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php";
// require db queries, do not put query in document
?>
<!-- Main container -->
<section class="hero is-light">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/components/navigation.php";?>
    <!-- Title and Filter -->
    <div class="hero-body">
        <div class="has-text-centered">
            <h1 class="title is-white is-unselectable">
                SpeedRun Comparator
            </h1>
            <h2 class="subtitle is-unselectable">
                Filters
            </h2>
            <!-- Filter options -->
            <form class="field">
                <div class="form-body">
                    <div class="field-body">
                        <!-- Monster Selection -->
                        <div class="field-label is-normal">
                            <label class="label is-unselectable">Monster</label>
                        </div>
                        <div class="field has-addons">
                            <p class="control">
                                <span class="select">
				    <form action="" method="post">
                                    <select id="difficulty" name='states' onchange="fillSelect(this.value, this.form['diff'])">
            							<option value="-1">Difficulty</option>
            							<option value="Low">Low</option>
            							<option value="High">High</option>
            							<option value="Tempered">Tempered</option>
                                    </select>

                                </span>
                            </p>
                            <p class="control">
                                <span class="select">
                                    <select name='diff'>
                                        <option>Select Monster</option>
					                    <p id="type"></p>
                                    </select>
                                </span>
                            </p>
                            <p class="control">
                                <a class="button is-dark" id="filterButton">Search</a>
                            </p>
                        </div>




                        <!-- Weapon Selection -->
                        <div class="field-label is-normal">
                            <label class="label is-unselectable">Weapon</label>
                        </div>
                        <div class="field has-addons">
                            <p class="control">
                                <span class="select">
                                    <select name='states' onchange="typeSelect(this.value,this.form['type'])">
                                        <option value="">Select Type</option>
                        				<option value="GSD">Great Sword</option>
                        				<option value="LSD">Long Sword</option>
                        				<option value="HAM">Hammer</option>
                        				<option value="HTH">Hunting Horn</option>
                        				<option value="DBL">Dual Blades</option>
                        				<option value="SAS">Sword and Sheild</option>
                        				<option value="LAN">Lance</option>
                        				<option value="GUL">Gun Lance</option>
                        				<option value="AWS">Switch Axe</option>
                        				<option value="CHB">Charge Blade</option>
                        				<option value="ING">Insect Glaive</option>
                        				<option value="BOW">Bow</option>
                        				<option value="LBG">Light Bowgun</option>
                        				<option value="HBG">Heavy Bowgun</option>
                        				<option value="EVT">Event Weapons</option>
                                    </select>
                                </span>
                            </p>
                            <p class="control">
				<div id="select">
                                <span class="select">
				<form action="" method="post">
                                    <select name='type'>
                                        <option value="">Select Weapon</option>
                                    </select>
                                </span>
				</div>
                            </p>
                            <p class="control">
                                <!-- Toggles is-dark on click-->
                                <a class="button" id="filterTASToggle" onclick="tasToggle()">
                                    Tool Assisted
                                </a>
                            </p>
                        </div>
                        <!-- JS/PHP Hunter Search -->
                        <div class="field-label is-normal">
                            <label class="label is-unselectable">Hunter</label>
                        </div>
                        <div class="field has-addons">
                            <div class="control">
                                <input class="input" type="text" placeholder="Hunter ID / Empty for All">
                            </div>
                            <div class="control">
                                <a class="button is-dark">
                                    Filter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <hr>
        <script>
        var currentWeaponType = document.querySelect('[name="type"]');
        </script>
        <!-- Main Container: Tiles / Graphs / Tables -->
        <section role="main container">
            <!-- Summary Card -->
            <div class="tile is-ancestor">
                <div class="tile is-parent">
                    <!-- Info Card -->
                    <div class="tile is-2 is-child">
                        <div class="card has-text-centered is-wide">
                            <div class="card-image">
 				<canvas id="WeaponRadarChart" width="100" height="100"></canvas>
				<script>
console.log("target");
    				let ctx = document.getElementById("WeaponRadarChart")
  				let WeaponRadarChart = new Chart(ctx, {
      				type: 'radar',
       					data: {
           				 labels: [
            				    "Great Sword",
            				    "Sword & Shield",
              				    "Dual Blades",
                			    "Long Sword" ,
               				    "Hammer",
                			    "Hunting Horn",
                		    	    "Lance",
               				    "Gunlance",
               			   	    "Switch Axe",
               			 	    "Charge Blade" ,
               				    "Insect Glavie" ,
               			 	    "Bow" ,
               				    "Light Bowgun" ,
               				    "Heavy Bowgun",
                			    "Event"
           					 ],
            			datasets: [{
             		        label: 'Amount of Weapons use',
                		data: [
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='GSD'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='SAS'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='DBL'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='LSD'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='HAM'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='HTH'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='LAN'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='GUL'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='AWS'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='CHB'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='ING'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='BOW'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='LBG'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='HBG'")->fetchColumn();
							echo $weapon ?> ,
					<?php
						$weapon = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS WHERE type='EVT'")->fetchColumn();
							echo $weapon ?> ,
       					],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)'
               			 ],
                borderColor: [
                    'rgba(255,99,132,1)',
                ],
                pointBorderColor: [
                   	 <?php
                    		$count = 14;
                   		 $array = [];

                    		while ($count-- > 0) {
                    		   array_push($array , "'rgba(255,99,132,1)'");
                   		 }
 		                   echo implode(",", $array);
                	    ?>
                ],
                pointBackgroundColor: [
                  	  <?php
                    		$count = 15;
                   		 $array = [];

                    		while ($count-- > 0) {
                     		   array_push($array , "'rgba(255,99,132,1)'");
                  		  }

                   		 echo implode(",", $array);
                    	?>
                ],

            }]
        },
      		 options: {
              	 layout: {
            }
        }
    })
</script>

                                <figure></figure>
                            </div>
                            <div class="card-content">
                                <div class="media">
                                    <h1>
                                        <strong>Top Hunters</strong>
                                    </h1>
                                </div>
                                <div class="content">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>UserId</th>
                                                <th>Username</th>
                                                <th>Hunts</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                              <?php
                                	$stmt = $pdo->query('SELECT USERS_RUNS.userId, username, COUNT(USERS_RUNS.userId) AS `value_occurrence`
FROM  USERS_RUNS
JOIN USERS ON USERS.userId = USERS_RUNS.userId
GROUP BY userId
ORDER BY `value_occurrence` DESC
LIMIT 3');
                                foreach ($stmt as $row) {
                                    echo "<tr> <th> " . $row['userId'] . "</th> <td>", $row['username'] . "</td> <td>",  $row['value_occurrence'] .  "</td> </tr>";
                                }
                                ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- JS/PHP replace # with a number-->
                            <footer class="card-footer">
                                <a hreff="#" class="card-footer-item">Hunters:
                                    <?php
                                        $count = $pdo->query("SELECT count(*) FROM USERS")->fetchColumn();
                                        echo $count // TODO: move to top of page
                                    ?> 
			</a>

                                <a hreff="#" class="card-footer-item">Runs:
                                    <?php
                                        $count = $pdo->query("SELECT count(*) FROM RUNS_WEAPONS_MAPS_MONSTERS")->fetchColumn();
                                        echo $count
                                    // TODO: move to top of page
                                    ?>
	</a>
                            </footer>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="tile is-4 is-child">
                        <!-- TODO: JS/PHP replace the data here according to which tab people clicked above -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>RunId</th>
                                    <th>Name</th>
				    <th>Monster</th>
                                    <th>Time/Link</th>
                                    <th>Date[DD/MM/YY]</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                	$stmt = $pdo->query('SELECT DISTINCT RUNS.runId, username, MONSTERS.name, weaponName, time, submitedAt
								FROM USERS
								JOIN USERS_RUNS ON USERS.userId = USERS_RUNS.userId
								JOIN RUNS ON RUNS.runId = USERS_RUNS.runId
								JOIN RUNS_WEAPONS_MAPS_MONSTERS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
								JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId
								JOIN WEAPONS ON WEAPONS.weaponId = RUNS_WEAPONS_MAPS_MONSTERS.weaponId AND WEAPONS.type = RUNS_WEAPONS_MAPS_MONSTERS.type AND WEAPONS.tree = RUNS_WEAPONS_MAPS_MONSTERS.tree
								ORDER BY runId
								DESC LIMIT 10');
                                foreach ($stmt as $row) {
                                    echo "<tr> <th> " . $row['runId'] . "</th> <td>", $row['username'] . "</td> <td>", $row['name'] . "</td> <td>",  $row['time'] . "</td> <td>", $row['submitedAt'] . "</td> </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Graph -->
                    <div class="tile is-6 is-child">
                        <canvas id="MonsterAverage"></canvas>
                        <script>
                            var ctx1 = document.getElementById("MonsterAverage");
                            var MonsterAverage = new Chart(ctx1, {
                                type: 'horizontalBar',
                                data: {
                                labels: [

					"Anajanath" ,
					"Azure Rathalos" ,
                                 	"Barroth" ,
					"Bazelgeuse" ,
					"Devilijho" ,
                                    	"Diablos",
					"Black Diablos",
					"Dodogama",
                                    	"Great Girros",
                                    	"Great Jagras",
                                    	"Jyuratodus",
                                    	"Kirin",
                                    	"Kulu-Ya-Ku",
					"Kushala Daora",
					"Lavasioth",
                                    	"Legiana",
					"Nergigante",
                                    	"Odigaron",
                                    	"Paolumu",
                                    	"Pukei-Pukei",
                                    	"Radobaan",
                                    	"Rathalos",
                                    	"Rathian",
					"Pink Rathian",
					"Teostra",
                                    	"Tobi-Kadachi",
                                    	"Tzitzi-Ya-Ku",
					"Uragaan",
					"Vaal Hazak",
					"Xeno'jiiva",
                                    	"Zorah Magdaros",
    					document.getElementById("target"),
                                    ],






                                datasets: [{
                                    label: 'Average Run time Per Monster',
                                    data: [

<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '1'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '2'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '3'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '4'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '5'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '6'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '7'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '8'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '9'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '10'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '11'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '12'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '13'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '14'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '15'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '16'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '17'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '18'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '19'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '20'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '21'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '22'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '23'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '24'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '25'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '26'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '27'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '28'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '29'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '30'")->fetchColumn(); echo $monster
?> ,
<?php
$monster = $pdo->query("SELECT AVG(time) FROM RUNS_WEAPONS_MAPS_MONSTERS JOIN MONSTERS ON MONSTERS.monsterId = RUNS_WEAPONS_MAPS_MONSTERS.monsterId JOIN RUNS ON RUNS.runId = RUNS_WEAPONS_MAPS_MONSTERS.runId
WHERE MONSTERS.monsterId = '31'")->fetchColumn(); echo $monster
?> ,
				],
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)',
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)',
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)',
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)',
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)',
                                        'rgba(153, 102, 255, 0.2)',
                                        'rgba(255, 159, 64, 0.2)',
                                        'rgba(255, 99, 132, 0.2)',
                                          ],
                                    borderColor: [
                                        'rgba(255,99,132,1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(255,99,132,1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(255,99,132,1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(255,99,132,1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(255,99,132,1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(255,99,132,1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)',
                                        'rgba(255,99,132,1)',
                                        'rgba(54, 162, 235, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                                },
                                options: {
                                        layout:
                                        {
                                        }
                                        }
                            });

                        </script>

<script>
var runs = [
<?php
$stmt = $pdo->query('SELECT name, difficulty FROM MONSTERS');
while ($row = $stmt->fetch())
{ ?>
{name: "<?php echo $row['name']; ?>", weaponType: "<?php echo $row['difficulty'];?>" },
<?php
}
?>



];

var filterRuns = function (weaponType) {
  var weaponType = document.querySelector("#difficulty").value;
  var filteredRuns = runs.filter(function (run) {
    if (weaponType === "-1") return true;

    return run.weaponType === weaponType;
  });
  console.log(weaponType)
  console.log(filteredRuns);

  // render the runs
  var output = "";
  filteredRuns.forEach(function (run) {
     output += (
       `${run.name}<br />`
    )
  });

  var target = document.getElementById("target")
  target.innerHTML = output;


}

document.querySelector('#filterButton').addEventListener('click', function(e) {
  filterRuns();
});

filterRuns();


</script>
                        <figure></figure>



                    </div>
                </div>
            </div>
        </section>
    </div>
</section>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/components/runs-modal.php";?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/components/login-form.php";?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/components/registration-form.php";?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/components/account-modal.php";?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php";?>
