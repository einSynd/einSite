<div class="full">
	<div class="img"><img src="<?php echo $thumb;?>"></img></div>
	<div class="info" id="<?php if($ID > 0){ echo $ID; }?>">
		<div class="title" <?php if($ID > 0){echo "onclick=\"makePopup('" . $_GET["stream"] . "'," . $ID . ")\"";} ?>><?php echo $title; ?></div>
		<br />
		<div class="desc"><?php echo $desc; ?></div>
		<br />
		<div class="created"><?php echo $created; ?></div>
		<br />
		<div class="footer">
			<div class="numParts">
				<?php 
					if($parts > 0){echo ($parts == 1) ? $parts . " part" : $parts . " parts";}	
				?>
			</div>
			<div class="length">
				<?php
					if($length > 0){echo "Length: " . gmdate("H:i:s", $length);} ?></div>
		</div>
	</div>
</div>