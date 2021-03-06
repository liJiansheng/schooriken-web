<?php 
require_once("session.php");
require_once("events.php");
require_once("common.php");

$sess->authPage(2);
if (isset($_POST['userID'])) {
	if (events::validateEvents($_POST)) {
        $announce_id = events::addEvents($_POST);
		/*if ($_FILES['picture']['name'] != "")
		{
			if (events::validateImage($event_id, $_FILES)) {
				$isUpload = events::attachPhoto($event_id, $_FILES['picture']);
			}
		}*/
        if (isset($_POST['img_url'])) {
            events::attachPhotoUrl($announce_id, $_POST['img_url']);
        }
		successMessage("Announcement added ", 'viewAnnounce.php?announce_id='.$announce_id);
		die(0);
	}
} else {
    $tmpdirname = md5((string)$_SESSION['userID']."_".(string)time());
}
$announce = $_POST;
//$subject['category'] = implode(",", $subject['category']);
require_once("top.php");
?>

<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="jQueryFileUpload/css/jquery.fileupload.css">
<link rel="stylesheet" href="jQueryFileUpload/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="jQueryFileUpload/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="jQueryFileUpload/css/jquery.fileupload-ui-noscript.css"></noscript>

<script src="jQueryFileUpload/js/vendor/jquery.ui.widget.js"></script>
<script src="js/load-image.min.js"></script>
<script src="js/canvas-to-blob.min.js"></script>
<script src="jQueryFileUpload/js/jquery.iframe-transport.js"></script>
<script src="jQueryFileUpload/js/jquery.fileupload.js"></script>
<script src="jQueryFileUpload/js/jquery.fileupload-process.js"></script>
<script src="jQueryFileUpload/js/jquery.fileupload-image.js"></script>
<script src="jQueryFileUpload/js/jquery.fileupload-validate.js"></script>

<div class="container"><div class="row"><div class="col-md-3">
<?php echo events::navBar($mydata['userID'],'Announcement'); ?></div>
<div class="col-md-8"><h2>Add Announcement</h2>
<form name="addEvent" class='form-horizontal' enctype="multipart/form-data" method='post' id="formAddEvent" action=""><fieldset>
	<div class='form-group'>
		<label class="col-md-4" for="title">Announcement Name:</label>
		<input type="text" id="title" name="title" maxlength="150" class="form-control" value="<?php echo htmlentities($announce['title']); ?>">
	</div>
	   <div class='form-group'>
      <label class='col-md-4' rel='tooltip'>Date of Announcement:</label>
         <div id="datetimepicker" class="input-append">
    <input id="evtDate" name="evtDate" data-format="dd-MM-yyyy" type="text" value="<?php echo htmlentities($announce['evtDate']); ?>"></input>
    <span class="add-on">
      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
      </i>
    </span>
  </div>
	</div>
	 <div class='form-group'>
		<label class='col-md-3' rel='tooltip'>Groups:</label>
            <select name='group_id' value="<?php echo htmlentities($announce['group_id']);?>" class='col-md-3'>
			<?php
			$grpList = events::getGroupList($mydata['schoolID']);
				foreach($grpList as $k => $v) {
				echo "<option value=".$v['group_id'].">".$v['group_name']."</option>";			
				}?>
		</select>
	</div>
	
	<div class='form-group'>
		<label class="col-md-3" for="description">Description:</label>
			<textarea id="description" name="description" class="form-control" rows="5" maxlength="400" placeholder="Announcement Description">
<?php echo htmlentities($announce['description']); ?>
</textarea>
	</div>
	  <div class='form-group'> 
	   <!--<label class='col-md-3' rel='tooltip'>Add Event Image:</label></br>
            <p><input type="file" size="32" name="picture" value="" id="picture" /></p>
            <p class="button"><input type="hidden" name="action" value="img" /><input type='hidden' name='MAX_FILE_SIZE' value='100000'>-->
            <span class="btn btn-default fileinput-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Add Image...</span>
                <input id='fileupload' type='file' name='files[]' data-url='uploadImage.php'>
            </span>
		<div id="files" class="files"></div>
        <div id="imguploaddiv">
            <table><tr>
                <td>
                    <span class="preview"><img src="#" id="previewimg" /></span>
                </td>
                <td>
                    <p class="name"></p>
                    <strong class="error text-danger"></strong>
                </td>
                <td width="80%">
                    <p class="size" id="imguploadstatus">Processing...</p>
                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="imgprogress"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
                </td>
            </tr></table>
        </div>
        <div id="dnd_result"></div>
    </div>
    <div class='form-group'>
		<button type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit">Add Announcement</button>
		<button type="reset" class="btn clear">Clear</button>
	</div>
 <input type="hidden" id="type" name="type" class="col-md-8" placeholder="" value="Announcement">	
	 <input type="hidden" id="userID" name="userID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['userID']);?>">
    <input type="hidden" id="schoolID" name="schoolID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['schoolID']); ?>">

	
</fieldset>
</form>
</div></div></div>
<script type="text/javascript">
  $(function() {
    var submitted = false;
    $('#imguploaddiv').hide();
    $('#datetimepicker').datetimepicker({
      pickTime: false
    });
 'use strict';
    // Change this to the location of your server-side upload handler:
    var url ='uploadImage.php', 
        uploadButton = $('<button/>')
            .addClass('btn btn-primary')
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function () {
                var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    .text('Abort')
                    .on('click', function () {
                        $this.remove();
                        data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });
            });
			 var submitDat = null;
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',      
        acceptFileTypes: /(\.|\/)(gif|jpeg|png)$/i,
        maxFileSize: 5000000, // 5 MB
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: false,
        previewMaxWidth: 500,
        previewMaxHeight: 300,
		imageMaxWidth: 500,
    imageMaxHeight: 500,
        previewCrop: true
    }).on('fileuploadadd', function (e, data) {	
	submitDat = data;
		   data.context = $('#files');
        $.each(data.files, function (index, file) {
			$('#files').html("");
           var node = $('<p/>')
                    .append($('<span/>').text(file.name));
            if (!index) {
                node
                    .append('<br>')                  
            }
            node.appendTo(data.context);
        });
    }).on('fileuploadprocessalways', function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        if (file.preview) {
            node
                .prepend('<br>')
                .prepend(file.preview);
        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
        }
        if (index + 1 === data.files.length) {
            data.context.find('button')
                .text('Upload')
                .prop('disabled', !!data.files.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#imgprogress .progress').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
		
        $.each(data.result.files, function (index, file) {
            if (file.url) {
                var link = $('<a>')
                    .attr('target', '_blank')
                    .prop('href', file.url);
                $(data.context.children()[index])
                    .wrap(link);					
			
			var form = document.getElementById('formAddEvent');		      
        if (form.img_url == undefined) {
            var imgURL = document.createElement("input");
            imgURL.type = 'hidden';
            imgURL.name = "img_url";
            imgURL.value = file.url;
            form.appendChild(imgURL);
        } else {
            form.img_url = file.url;
        }
        
        submitDat = null;
        if (submitted) {
            form.submit();
        }
            } else if (file.error) {
                var error = $('<span class="text-danger"/>').text(file.error);
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            }
        });
    }).on('fileuploadfail', function (e, data) {
        $.each(data.files, function (index, file) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
		
	  $('#btnSubmit').click(function(e){
        if (submitDat) {
            e.preventDefault();
            submitDat.submit();
            submitted = true;
            return false;
        }
        return true;
    });
  });
</script>
<?php
require_once("newtail.php");
?>
