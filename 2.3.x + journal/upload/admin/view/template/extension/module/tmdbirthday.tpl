<?php echo $header; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet"/>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
		<button type="submit" form="form-tmdbirth" onclick="$('#form-tmdbirth').attr('action','<?php echo $staysave; ?>');$('#form-tmdbirth').submit(); " data-toggle="tooltip" title="" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_savestay; ?></button>
        <button type="submit" form="form-tmdbirth" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
       <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-tmdbirth" class="form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active" ><a href="#tab-setting" data-toggle="tab"><i class="fa fa-cogs"></i> <?php echo $tab_setting; ?></a></li>
				<li><a href="#tab-giftmail" data-toggle="tab"><i class="fa fa-envelope-o"></i> <?php echo $tab_giftmail; ?></a></li>
				<li><a href="#tab-calendar" data-toggle="tab"><i class="fa fa-calendar"></i> <?php echo $tab_calendar; ?></a></li>
				<li><a href="#tab-cronjon" data-toggle="tab"><i class="fa fa-refresh"></i> <?php echo $tab_cronjon; ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab-setting">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
						<div class="col-sm-10">
							<label class="tmdswitch inputbundle4">
							  <input type="checkbox" value="1" name="tmdbirthday_status" <?php if ($tmdbirthday_status) { ?> checked="checked" <?php } ?>>
							  <span class="tmdslider round"></span>
							  <span class="tmdabsolute-no"><?php echo $text_no; ?></span>
							</label>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-coupon"><?php echo $entry_code; ?></label>
						<div class="col-sm-6">
							<div class="input-group">
								<input type="text" name="tmdbirthday_coupons" readonly value="<?php echo $tmdbirthday_coupons; ?>" placeholder="<?php echo $entry_code; ?>" id="input-coupon" class="form-control" />
								<span class="input-group-btn">
				                <button type="button" id="gettokens" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-info"><i class="fa fa-ticket"></i> <?php echo $entry_code; ?></button>
				                </span>
							</div>
						</div>
					</div>
						
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-valid"><?php echo $entry_date; ?></label>
						<div class="col-sm-10">
						  <div class="row">
							<div class="col-sm-6">
								<div class="input-group date">
									<input type="text" name="tmdbirthday_start" value="<?php echo $tmdbirthday_start; ?>" data-date-format="YYYY-MM-DD" placeholder="<?php echo $entry_start; ?>" id="input-start" class="form-control" />
									<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="input-group date1">
									<input type="text" name="tmdbirthday_end" value="<?php echo $tmdbirthday_end; ?>" data-date-format="YYYY-MM-DD" placeholder="<?php echo $entry_end; ?>" id="input-end" class="form-control" />
									<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
						  </div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-dis_type"><?php echo $entry_dis_type; ?></label>
						<div class="col-sm-6">
						  <select name="tmdbirthday_dis_type" id="input-dis_type" class="form-control">
							<?php foreach($types as $type){?>
							<?php if($type['value'] == $tmdbirthday_dis_type) { ?>
							<option value="<?php echo $type['value']; ?>" selected="selected"><?php echo $type['text']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $type['value']; ?>"><?php echo $type['text']; ?></option>
							<?php } ?>
							<?php } ?>
						  </select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-dis_value"><?php echo $entry_dis_value; ?></label>
						<div class="col-sm-6">
							<input type="number" name="tmdbirthday_dis_value" value="<?php echo $tmdbirthday_dis_value; ?>" placeholder="<?php echo $entry_dis_value; ?>" id="input-dis_value" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-total"><?php echo $entry_total; ?></label>
						<div class="col-sm-6">
							<input type="number" name="tmdbirthday_total_amount" value="<?php echo $tmdbirthday_total_amount; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-use_coupon"><?php echo $entry_use_coupon; ?></label>
						<div class="col-sm-6">
							<input type="number" name="tmdbirthday_use_coupon" value="<?php echo $tmdbirthday_use_coupon; ?>" placeholder="<?php echo $entry_use_coupon; ?>" id="input-use_coupon" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-use_customer"><?php echo $entry_use_customer; ?></label>
						<div class="col-sm-6">
							<input type="number" name="tmdbirthday_use_customer" value="<?php echo $tmdbirthday_use_customer; ?>" placeholder="<?php echo $entry_use_customer; ?>" id="input-use_customer" class="form-control" />
						</div>
					</div>
				</div>
				<div class="tab-pane" id="tab-giftmail">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-subject"><?php echo $entry_subject; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="tmdbirthday_subject" value="<?php echo $tmdbirthday_subject; ?>" id="input-subject" placeholder="<?php echo $entry_subject; ?>" class="form-control"/>
						</div>
					</div>	
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-message"><?php echo $entry_message; ?></label>
						<div class="col-sm-10">
						  	<textarea type="text" name="tmdbirthday_message" data-toggle="summernote" data-lang="<?php echo $summernote; ?>" id="input-message" placeholder="<?php echo $entry_message; ?>" class="form-control summernote"><?php echo $tmdbirthday_message; ?></textarea>
							<span style="color:#23A8DA;"><b>Shortcuts : {firstname} {lastname} {coupon_code} {discount_value} {total_amount} {end_date}</b></span>
						</div>
					</div>				
				</div>
				
				<div class="tab-pane" id="tab-calendar">
					<div class="panel panel-default">
				      	<div class="panel-heading">
				        	<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_heading; ?></h3>
				      	</div>
				      	<div class="panel-body">
        					<form action="" method="post" enctype="multipart/form-data" id="form-attribute">
								<div class="table-responsive">
									<table class="table table-bordered table-hover">
									  <thead>
										<tr>
											<td class="text-left bgc1"><?php echo $column_apply; ?></td>
											<td class="text-left bgc1"><?php echo $column_name; ?></td>
											<td class="text-left bgc1"><?php echo $column_email; ?></td>
											<td class="text-left bgc1"><?php echo $column_dob; ?></td>
											<td class="text-left bgc1"><?php echo $column_group; ?></td>
											<td class="text-left bgc1"><?php echo $column_status; ?></td>
											<td class="text-left bgc1"><?php echo $column_date; ?></td>
											<td class="text-right bgc1"><?php echo $column_action; ?></td>
										</tr>
									  </thead>
									  <tbody class="allcustomers">
										
									  </tbody>
									</table>
								</div>
							</form>
						</div>
					</div>
					<?php echo $tmdcalender; ?>
					<div class="form-group hide">
						<label class="col-sm-2 control-label" for="input-message"></label>
						<div class="col-sm-10">
						  	<?php echo $tmdcalender; ?>
						</div>
					</div>					
				</div>

				<div class="tab-pane" id="tab-cronjon">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-cronjobstatus"><?php echo $entry_cronstatus; ?></label>
						<div class="col-sm-10">
							<select name="tmdbirthday_cronjobstatus" id="input-status" class="form-control">
								<?php if ($tmdbirthday_cronjobstatus) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
							<?php if ($tmdbirthday_cronjobstatus==1) { ?>
							<strong><a href="<?php echo $cronjobstatus; ?>" target="_new"><?php echo $cronjobstatus; ?></a></strong>
							<?php } ?>
						</div>
					</div>
				</div>

			</div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
$('.date1').datetimepicker({
	pickTime: false
});

//--></script>

<div class="modal" id="myModal" role="dialog" aria-labelledby="myModal" aria-hidden="true"></div>
<script>
$(document).on('click','.tmdcustomer',function(e) {
  $('#myModal .modal-dialog').html('<div class="loader-if centered"></div>');
   $('#myModal').load($(this).attr('href'));
  
});
</script>

<script type="text/javascript"><!--
$(document).on('click','.tmdgiftadd',function() {
  rel=$(this).attr('rel');
  $.ajax({
    url: 'index.php?route=extension/module/tmdbirthday/addgift&token=<?php echo $token; ?>&customer_id='+rel,
    type: 'post',
    data: $('.giftnow'+rel+' input[type=\'text\'], .giftnow'+rel+' input[type=\'hidden\'], .giftnow'+rel+' select'),
    dataType: 'json',
    beforeSend: function() {
		$('.tmdgiftadd').button('loading');
	},
	complete: function() {
		$('.tmdgiftadd').button('reset');
	},
    success: function(json) {
      $('.alert, .text-danger').remove();
		if (json['error']) {
			if (json['error']['error_date_start']) {
			  $('.error_date_start').after('<div class="text-danger">' + json['error']['error_date_start'] + '</div>');
			}
			if (json['error']['error_date_end']) {
			  $('.error_date_end').after('<div class="text-danger">' + json['error']['error_date_end'] + '</div>');
			}
			if (json['error']['error_dis_val']) {
				$('input[name=\'dis_value\']').after('<div class="text-danger">' + json['error']['error_dis_val'] + '</div>');
			}
			
			if (json['error']['error_total']) {
				$('input[name=\'total_amount\']').after('<div class="text-danger">' + json['error']['error_total'] + '</div>');
			}

			if (json['error']['error_coupon_exit']) {
				$('.tmd-alert').after('<div class="alert alert-danger alert-dismissible">' + json['error']['error_coupon_exit'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				$('.error_date_start').after('<div class="text-danger">' + json['error']['error_start_date'] + '</div>');
				$('.error_date_end').after('<div class="text-danger">' + json['error']['error_end_date'] + '</div>');
			}
		}
		if (json['success']) {
			$('#myModal').modal('hide');
			$('.breadcrumb').after('<div class="alert alert-success alert-dismissible">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		}
    },
  });
});
//--></script>

<script type="text/javascript">
$(document).on('click','#gettokens',function() {
  $.ajax({
    url: 'index.php?route=extension/module/tmdbirthday/generateCoupon&token=<?php echo $token; ?>',
    type: 'post',
    data: '',
    dataType: 'json',
    beforeSend: function() {
    	$('#gettokens').button('loading');
    },
    complete: function() {
    	$('#gettokens').button('reset');
    },
    success: function(json) {
      if (json['success']) {
        $('#input-coupon').val(json['code']);
      }
    },
  });
});
</script>

<style>
.bgc1{background: #48c0f0;color: #fff;padding: 12px;}
</style>

<style>
.tmdminicolors-theme-bootstrap .tmdminicolors-input{
	width:100%;
	height:35px;
}
.tmdcomponent td:first-child{
	cursor:move; 
}
.tmdquickbtn-success {
	color: #fff;
	background-color: #afafaf;
	border-color: #afafaf;
}
.tmdquickbtn-success:hover,.tmdquickbtn-success:active:hover, .tmdquickbtn-success.active:hover, .open > .tmdquickbtn-success.dropdown-toggle:hover, .tmdquickbtn-success:active:focus, .tmdquickbtn-success.active:focus, .open > .tmdquickbtn-success.dropdown-toggle:focus, .tmdquickbtn-success:active.focus, .tmdquickbtn-success.active.focus, .open > .tmdquickbtn-success.dropdown-toggle.focus, .tmdquickbtn-success:active, .tmdquickbtn-success.active, .open > .tmdquickbtn-success.dropdown-toggle{
	background-color:#33ccff;
	border-color:#33ccff;
}
.tmdswitch {
  position: relative;
  display: inline-block;
  width: 95px;
  height: 30px;
}

.tmdswitch input {display:none;}

.tmdslider {
  position: absolute;
  cursor: pointer;
  overflow: hidden;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #f2f2f2;
  -webkit-transition: .4s;
  transition: .4s;
}

.tmdslider:before {
  position: absolute;
  z-index: 2;
  content: "";
  height: 22px;
  width: 22px;
  left: 4px;
  bottom: 4px;
  background-color: #2aabd2;
      -webkit-box-shadow: 0 2px 5px rgba(0, 0, 0, 0.22);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.22);
  -webkit-transition: .4s;
  transition: all 0.4s ease-in-out;
}
.tmdslider:after {
  position: absolute;
  left: 0;
  z-index: 1;
  content: "YES";
    font-size: 14px;
    text-align: left !important;
    line-height: 30px;
	padding-left: 0;
    width: 95px;
    color: #fff;
    height: 30px;
    border-radius: 100px;
    background-color: #2aabd2;
    -webkit-transform: translateX(-160px);
    -ms-transform: translateX(-160px);
    transform: translateX(-160px);
    transition: all 0.4s ease-in-out;
}

input:checked + .tmdslider:after {
  -webkit-transform: translateX(0px);
  -ms-transform: translateX(0px);
  transform: translateX(0px);
  /*width: 235px;*/
  padding-left: 15px;
}

input:checked + .tmdslider:before {
  background-color: #fff;
}

input:checked + .tmdslider:before {
  -webkit-transform: translateX(63px);
  -ms-transform: translateX(63px);
  transform: translateX(63px);
}

/* Rounded arsliders */
.tmdslider.round {
  border-radius: 100px;
}

.tmdslider.round:before {
  border-radius: 50%;
}
.tmdabsolute-no {
position: absolute;
left: 0;
color: #2aabd2;
text-align: right !important;
font-size: 14px;
width: calc(100% - 15px);
height: 30px;
line-height: 30px;
cursor: pointer;
} 

#form-tmdbirth ul li.active > a,#form-tmdbirth ul li.active > a:hover,#form-tmdbirth ul li.active > a:focus{
	background: #00a4e4 none repeat scroll 0 0 !important;
	color:#fff;
}
#form-tmdbirth .nav-tabs li a{
	background:#E4E6EA;
}
#form-tmdbirth .nav-tabs > li.active > a, #form-tmdbirth .nav-tabs > li.active > a:hover,#form-tmdbirth .nav-tabs > li.active > a:focus{
	color:#fff;
}

.datepicker table tr td.active.disabled.active{
	/*background:red none repeat scroll 0 0 !important;*/
	background:#fff;
	color:#999;
}
.datepicker table tr td.active.disabled:hover.active, .datepicker table tr td.active.disabled:hover.disabled, .datepicker table tr td.active.disabled:hover:active, .datepicker table tr td.active.disabled:hover:hover, .datepicker table tr td.active.disabled:hover[disabled], .datepicker table tr td.active.disabled[disabled],.datepicker table tr td.active:hover.disabled,.datepicker table tr td.active:hover[disabled], .datepicker table tr td.active[disabled] {
	background:#fff;

}
</style>
<?php echo $footer; ?>