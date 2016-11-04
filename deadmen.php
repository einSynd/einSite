<html><head>
<title>Deadman's Cross Card List</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript"></script>
<script src="sorttable.js"></script>
<style>
/* Sortable tables */
table.sortable thead {
	background-color:#999999;
	font-weight: bold;
	cursor: default;
}

table {
	border-collapse:collapse;
}

table, th, td {
	border: 1px solid black;
}

td {
	padding: 3px;
}

.error {
	color: #900;
}

#shortName {
	width: 118px;
}
/* End Sortable Tables */

.Virulent, .Pestilent {
	display: none;
}

#toggleVirulent, #togglePestilent {
	display: inline;
}

.name {
	color: blue;
	text-decoration: underline;
}
</style>
</head>
<body>
<script type="text/javascript">
// Deadman Array PHP Dump Start
<?php
	$deadmanList = json_encode(json_decode(@file_get_contents("deadmen.json")));
	echo "var deadmanArray = " . $deadmanList . "\r\n";
?>
//Deadman Array PHP Dump End
function loadList(zone, rarity, strain) {
	$("#fullList tbody").empty();
	
	var zone = $("#zone").val();
	var rarity = $("#rarity").val();
	var strain = $("#strain").val();
	
	var virulents = $("#chkVirulent").is(":checked");
	var pestilents = $("#chkPestilent").is(":checked");
	
	var toAppend;
	var toLink;
	$.each(deadmanArray, function(index, deadman){
		if(zone == "Any" || deadman["Zone"] == zone ){
			if( rarity == "Any" || deadman["Rarity"] == rarity ){
				if( strain == "Any" || deadman["Strain"] == strain ){
					if( (typeof(deadman["Name"].split("Virulent")[1]) === 'undefined') || virulents === true) {
						if( (typeof(deadman["Name"].split("Pestilent")[1]) === 'undefined') || pestilents === true) {
							toAppend = "<tr>";
							toLink = false;
							if( deadman["Rarity"] >= 4 ) { toLink = true; }
							$.each(deadman, function(ind, stat){
								if( stat == null ){ stat = "---"; }
								if( toLink ) {
									toAppend += "<td class='" + ind + "'>" ;
								} else {
									toAppend += "<td>";
								}
								toAppend += stat + "</td>";
							});
							toAppend += "</tr>";
							$("#fullList tbody:first").append(toAppend);
						}
					}
				}
			}
		}
	});
	
	$(".name").click(function(){
		var name = this.innerHTML;
		window.open("http://dccards.io/calculator/?card="+name, "_blank");
	});
}

$(document).ready(function(){
	$("select").prop("selectedIndex", 0);

	$("select").change(function() {
		loadList();
	});
	
	$("#toggleVirulent").click(function(){
		if( $("#chkVirulent").is( ":checked") ){
			$("#chkVirulent").prop("checked", false);
		} else {
			$("#chkVirulent").prop("checked", true);
		}
		loadList();
	});
	
	$("#chkVirulent").click(function(){
		loadList();
	});
	
	$("#togglePestilent").click(function(){
		if( $("#chkPestilent").is( ":checked") ){
			$("#chkPestilent").prop("checked", false);
		} else {
			$("#chkPestilent").prop("checked", true);
		}
		loadList();
	});
	
	$("#chkPestilent").click(function(){
		loadList();
	});
	
	var loadThings = window.setTimeout(loadList, 1);
	$("#chkVirulent").prop("checked", false);
	$("#chkPestilent").prop("checked", false);
});


</script>
<table border="0" id="dropdownTable">
<tr>
<td>Zone: <select id="zone">
	<option value="Any">Any</option>
	<option value="Gould Square">Gould Square</option><option value="Clayton Cemetery">Clayton Cemetery</option>
	<option value="Tumbleweed Casino">Tumbleweed Casino</option><option value="USS Claypool">USS Claypool</option>
	<option value="ZenoTek Stadium">ZenoTek Stadium</option>
	</select>
</td>
<td>Rarity: <select id="rarity">
	<option value="Any">Any</option>
	<option value="1">*</option><option value="2">**</option><option value="3">***</option>
	<option value="4">****</option><option value="4.5">****+</option><option value="5">*****</option>
	</select>
</td>
<td>Strain: <select id="strain">
	<option value="Any">Any</option>
	<option value="Burner">Burner</option><option value="Charmer">Charmer</option>
	<option value="Chiller">Chiller</option><option value="Leecher">Leecher</option>
	<option value="Screamer">Screamer</option><option value="Shocker">Shocker</option>
	<option value="Slasher">Slasher</option><option value="Spitter">Spitter</option>
	</select>
</td>
<td><input type="checkbox" name="Virulent" id="chkVirulent" /><div id="toggleVirulent">Virulents</div></td>
<td><input type="checkbox" name="Pestilent" id="chkPestilent" /><div id="togglePestilent">Pestilents</div></td>
</tr>
</table>

<table border="1" id="fullList" class="sortable">
<tr>
	<th>Number</th>
	<th>Zone</th>
	<th>Silhouette</th>
	<th>Name</th>
	<th>Type</th>
	<th>Rarity</th>
	<th>Max HP</th>
	<th>Max PSY</th>
	<th>Max ATK</th>
	<th>Max DEF</th>
	<th>Max INT</th>
	<th>Max SPD</th>
	<th>Total Max</th>
	<th>Attack 1</th>
	<th>Attack 2</th>
	<th>Attack 3</th>
	<th>Attack 4</th>
	<th>Attack 5</th>
	<th>ReDeath</th>
</tr>
<tbody>
</tbody>
</table>
</body>
