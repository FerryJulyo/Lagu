<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<link rel="stylesheet" href="assets/table.css">
<link rel="stylesheet" href="<?= base_url() ?>assets/table.css">
<link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet">
<style>
	#modal-video,
	#modal-info {
		margin-top: 9em;
		margin-left: 12em;
	}

	.close {
		float: right;
		margin-top: -5em;
	}

	.pagination>a,
	.pagination>strong {
		float: left;
		margin-left: 5px;
		padding: 0 6px;
		min-width: 17px;
		line-height: 27px;
		font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size: 12px;
		font-weight: 500;
		color: #d4d4d4;
		text-align: center;
		text-decoration: none;
		border: 1px solid black;
		border-radius: 3px;
	}

	.pagination>strong,
	.pagination>a:active {
		color: #eee;
		text-shadow: 0 -1px black;
		background: #1c1c1c;
		background: rgba(255, 255, 255, 0.01);
		border-color: black rgba(0, 0, 0, 0.65) rgba(0, 0, 0, 0.6);
		-webkit-box-shadow: inset 0 1px rgba(0, 0, 0, 0.05), inset 0 2px 2px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.06);
		box-shadow: inset 0 1px rgba(0, 0, 0, 0.05), inset 0 2px 2px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.06);
	}

	.sb-search {
		position: relative;
		margin-top: 10px;
		width: 0%;
		min-width: 60px;
		height: 60px;
		float: right;
		overflow: hidden;
		-webkit-transition: width 0.3s;
		-moz-transition: width 0.3s;
		transition: width 0.3s;
	}

	.sb-search-input {
		position: absolute;
		top: 0;
		right: 0px;
		border: none;
		outline: none;
		background: #fff;
		width: 100%;
		height: 60px;
		margin: 0;
		z-index: 10;
		padding: 20px 65px 20px 20px;
		font-family: inherit;
		font-size: 20px;
		color: #2c3e50;
	}

	input[type="search"].sb-search-input {
		-webkit-appearance: none;
	}

	.sb-search-input::-webkit-input-placeholder {
		color: #efb480;
	}

	.sb-search-input:-moz-placeholder {
		color: #efb480;
	}

	.sb-search-input::-moz-placeholder {
		color: #efb480;
	}

	.sb-search-input:-ms-input-placeholder {
		color: #efb480;
	}

	.sb-icon-search,
	.sb-search-submit {
		width: 60px;
		height: 60px;
		display: block;
		position: absolute;
		right: 0;
		top: 0;
		padding: 0;
		margin: 0;
		line-height: 60px;
		text-align: center;
		cursor: pointer;
	}

	.sb-search-submit {
		background: #fff;
		/* IE needs this */
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
		/* IE 8 */
		filter: alpha(opacity=0);
		/* IE 5-7 */
		opacity: 0;
		color: transparent;
		color: red;
		border: none;
		outline: none;
		z-index: -1;
	}

	.sb-icon-search {
		color: #fff;
		background: #e67e22;
		z-index: 90;
		font-size: 22px;
		font-family: 'icomoon';
		font-style: normal;
		font-weight: normal;
		font-variant: normal;
		text-transform: none;
		-webkit-font-smoothing: antialiased;
	}

	.sb-icon-search:before {
		content: "";
	}

	.sb-search.sb-search-open,
	.no-js .sb-search {
		width: 100%;
	}

	.sb-search.sb-search-open .sb-icon-search,
	.no-js .sb-search .sb-icon-search {
		background: #da6d0d;
		color: #fff;
		z-index: 11;
	}

	.menu {
		float: right;
	}
</style>
</head>
<html lang="en">

<head>
</head>

<body>
	<h1>List Lagu</h1>
	<form action="<?= base_url('index.php/Home/search_data') ?>" method="post">
		<input type="text" name="keyword" placeholder="search..." value="<?= isset($keyword) ? $keyword : '' ?>">
		<input type="hidden" name="NotUse" value="1">
		<button type="submit">Search</button>
		<button type="button" class='open-modal-btn' data-toggle='modal' data-target='#upload'>Upload</button>
		<?php if ($type == 3) { ?>
			<a href="<?php echo base_url('index.php/Home/export'); ?>" onclick="return confirm('Apakah Anda yakin ingin melakukan ekspor?')"><button type="button">Export</button></a>
		<?php } ?>
		<div class="menu">
			<button type="button" name="duplicate_song" id="duplicate_song" <?php if ($type == 1) {
																				echo 'disabled';
																			} ?> onClick="window.location.href='<?= base_url('index.php/Home/lagu') ?>'">Semua Lagu</button>
			<button type="button" name="duplicate_song" id="duplicate_song" <?php if ($type == 2) {
																				echo 'disabled';
																			} ?> onClick="window.location.href='<?= base_url('index.php/Home/duplicate_song') ?>'">Lagu Kembar</button>
			<button type="button" name="miss_song" id="miss_song" <?php if ($type == 3) {
																		echo 'disabled';
																	} ?> onClick="window.location.href='<?= base_url('index.php/Home/miss_song') ?>'">Lagu Belum</button>
		</div>
	</form>
	<?php
	if ($type == 1) {
		if (count($results) > 0) { ?>
			<table class="rwd-table">
				<tr>
					<th>ID</th>
					<th>Ekstensi</th>
					<th>Judul</th>
					<th>Audio</th>
					<th>Width</th>
					<th>Height</th>
					<th>Date modified</th>
					<th>Detail</th>
					<th>View</th>
				</tr>
				<?php
				foreach ($results as $key) {
					$id = $key['id_file'];
					$extention = $key['extention'];
					$song = $key['song'];
					$metadata = $key['metadata'];
					$metadata = json_decode($metadata, true);
					$width = '';
					$height = '';
					$audio = 0;
					foreach ($metadata['streams'] as $keys) {
						if ($keys['codec_type'] == 'video') {
							$width = $keys['width'] . 'P';
							$height = $keys['height'] . 'P';
						}
						if ($keys['codec_type'] == 'audio') {
							$audio++;
						}
					}
					if ($audio == 2) {
						$audio = 'Music + Vocal';
					} else {
						$audio = 'Music';
					}
					$date_modified = $key['date_modified'];
					echo "
		<tr>
			<td>$id</td>
			<td>$extention</td>
			<td>$song</td>
			<td>$audio</td>
			<td>$width</td>
			<td>$height</td>
			<td>$date_modified</td>
			<td><button class='open-modal-btn detailVideo' data-id='$id' data-toggle='modal' data-target='#detailVideo'>Detail</button></td>
			<td><button class='open-modal-btn viewVideo' data-id='$id' data-toggle='modal' data-target='#viewVideo'>View</button></td>
		</tr>";
				}
				?>
			</table>
		<?php }
	} else if ($type == 2) {
		if (count($results['array1']) > 0) {
		?>
			<div style="display: flex; justify-content: space-between;">
				<table class="rwd-table" style="max-width: 49%;overflow:auto;">
					<tr>
						<th>ID</th>
						<th>Ekstensi</th>
						<th>Judul</th>
						<th>Date modified</th>
						<th>View</th>
					</tr>
					<?php
					foreach ($results['array1'] as $key) {
						$id = $key[0]['id_file'];
						$extention = $key[0]['extention'];
						$song = $key[0]['song'];
						$metadata = $key[0]['metadata'];
						$metadata = json_decode($metadata, true);
						$width = '';
						$height = '';
						$audio = 0;
						foreach ($metadata['streams'] as $keys) {
							if ($keys['codec_type'] == 'video') {
								$width = $keys['width'] . 'P';
								$height = $keys['height'] . 'P';
							}
							if ($keys['codec_type'] == 'audio') {
								$audio++;
							}
						}
						if ($audio == 2) {
							$audio = 'Music + Vocal';
						} else {
							$audio = 'Music';
						}
						$date_modified = $key[0]['date_modified'];
						echo "
		<tr>
			<td>$id</td>
			<td>$extention</td>
			<td>$song</td>
			<td>$date_modified</td>
			<td><button class='open-modal-btn viewVideo' data-id='$id' data-toggle='modal' data-target='#viewVideo'>View</button></td>
		</tr>";
					}
					?>
				</table>
			<?php }
		if (count($results['array2']) > 0) {
			?>
				<table class="rwd-table" style="max-width: 49%;overflow:auto;">
					<tr>
						<th>ID</th>
						<th>Ekstensi</th>
						<th>Judul</th>
						<th>Date modified</th>
						<th>View</th>
					</tr>
					<?php
					foreach ($results['array2'] as $key) {
						$id = $key[0]['id_file'];
						$extention = $key[0]['extention'];
						$song = $key[0]['song'];
						$metadata = $key[0]['metadata'];
						$metadata = json_decode($metadata, true);
						$width = '';
						$height = '';
						$audio = 0;
						foreach ($metadata['streams'] as $keys) {
							if ($keys['codec_type'] == 'video') {
								$width = $keys['width'] . 'P';
								$height = $keys['height'] . 'P';
							}
							if ($keys['codec_type'] == 'audio') {
								$audio++;
							}
						}
						if ($audio == 2) {
							$audio = 'Music + Vocal';
						} else {
							$audio = 'Music';
						}
						$date_modified = $key[0]['date_modified'];
						echo "
		<tr>
			<td>$id</td>
			<td>$extention</td>
			<td>$song</td>
			<td>$date_modified</td>
			<td><button class='open-modal-btn viewVideo' data-id='$id' data-toggle='modal' data-target='#viewVideo'>View</button></td>
		</tr>";
					}
					?>
				</table>
			</div>
		<?php }
	} else if ($type == 3) {
		if (count($results) > 0) { ?>
			<table class="rwd-table">
				<tr>
					<th>ID</th>
					<th>Ekstensi</th>
					<th>Judul</th>
					<th>Penyanyi</th>
				</tr>

				<?php
				foreach ($results as $key) {
					$id = $key['id'];
					$extention = $key['format'];
					$song = $key['song'];
					$csong = $key['csong'];
					echo "
		<tr>
			<td>$id</td>
			<td>$extention</td>
			<td>$song</td>
			<td>$csong</td>
		</tr>";
				}
				?>
			</table>
	<?php }
	} ?>
	<div class="container">
		<div class="pagination">
			<?= $pagination ?>
		</div>
	</div>
</body>

</html>
<!-- Modal Edit -->
<div class="modal fade" id="viewVideo" tabindex="-1" role="dialog" aria-labelledby="viewVideoLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" id="modal-info" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<strong>
					<h4 class="modal-title" id="viewVideoLabel">Info Video</h4>
				</strong>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- form edit data di sini -->
			</div>
			<div class="modal-footer">
				<!-- your footer content -->
			</div>
		</div>
	</div>
</div>
<!-- Modal Detail -->
<div class="modal fade" id="detailVideo" tabindex="-1" role="dialog" aria-labelledby="viewVideoLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" id="modal-video" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<strong>
					<h4 class="modal-title" id="viewVideoLabel">Detail</h4>
				</strong>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- form edit data di sini -->
			</div>
			<div class="modal-footer">
				<!-- your footer content -->
			</div>
		</div>
	</div>
</div>
<!-- Modal Detail -->
<div class="modal fade" id="upload" tabindex="-1" role="dialog" aria-labelledby="viewVideoLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" id="modal-video" role="document" style="width: 40%;">
		<div class="modal-content">
			<div class="modal-header">
				<strong>
					<h4 class="modal-title" id="viewVideoLabel">Upload lagu</h4>
				</strong>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="uploadForm" class="form" method="post" action="<?= base_url() ?>index.php/Home/upload" onsubmit="return checkUpload()" enctype="multipart/form-data">
				<label>Lokasi</label>
				<div>	
					<input style="margin: 0.5em;" type="radio" name='location' value="master_lagu_satu" checked></input>Master lagu satu
					<br>
					<input style="margin: 0.5em;" type="radio" name='location' value="master_lagu_dua"></input>Master lagu dua
					<br>
					<input style="margin: 0.5em;" type="radio" name='location' value="master_lagu_tiga"></input>Master lagu tiga
					<br>
					<input style="margin: 0.5em;" type="radio" name='location' value="master_lagu_empat"></input>Master lagu empat
					<br>
					<input style="margin: 0.5em;" type="radio" name='location' value="tampungan_lagu"></input>Tampungan lagu
					<br><br>
				</div>
				<label>File lagu</label><br>
				<input style="margin: 0.5em;" id="song_file" name="song_file" type="file" accept=".mpg, .mp4, .vob">
				</form>
			</div><br>
			<div class="modal-footer">
			<button type="submit" id="uploadBtn" form="uploadForm">Upload</button>
			</div>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
	$(document).ready(function() {
		// Ketika tombol edit ditekan
		$('.viewVideo').click(function() {
			var id = $(this).data('id');
			$.ajax({
				url: "<?= base_url() ?>index.php/Home/getVideo/" + id,
				method: 'POST',
				data: {
					id: id
				},
				success: function(response) {
					// Tampilkan data pada modal
					$('#viewVideo .modal-body').html(response);
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError);
				}
			});
		});
	});
	$(document).ready(function() {
		// Ketika tombol edit ditekan
		$('.detailVideo').click(function() {
			var id = $(this).data('id');
			$.ajax({
				url: "<?= base_url() ?>index.php/Home/detailVideo/" + id,
				method: 'POST',
				data: {
					id: id
				},
				success: function(response) {
					// Tampilkan data pada modal
					$('#detailVideo .modal-body').html(response);
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError);
				}
			});
		});
	});

	function checkUpload(){
		var file = document.getElementById('song_file');
		var btn = document.getElementById('uploadBtn');

		if(file.files.length == 0){
			alert('Silahkan pilih file lagu yang akan diupload!');
        return false;
		}

		btn.disabled = true;
	}

	function buttonUp() {
		var valux = $('.sb-search-input').val();
		valux = $.trim(valux).length;
		if (valux !== 0) {
			$('.sb-search-submit').css('z-index', '99');
		} else {
			$('.sb-search-input').val('');
			$('.sb-search-submit').css('z-index', '-999');
		}
	}

	$(document).ready(function() {
		var submitIcon = $('.sb-icon-search');
		var submitInput = $('.sb-search-input');
		var searchBox = $('.sb-search');
		var isOpen = false;

		$(document).mouseup(function() {
			if (isOpen == true) {
				submitInput.val('');
				$('.sb-search-submit').css('z-index', '-999');
				submitIcon.click();
			}
		});

		submitIcon.mouseup(function() {
			return false;
		});

		searchBox.mouseup(function() {
			return false;
		});

		submitIcon.click(function() {
			if (isOpen == false) {
				searchBox.addClass('sb-search-open');
				isOpen = true;
			} else {
				searchBox.removeClass('sb-search-open');
				isOpen = false;
			}
		});

	});
</script>