<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title"><?php echo $text_reminder; ?><?php echo $name; ?></h3>
    </div>
    <div class="modal-body">
		<div class="tmd-alert"></div>
		<form action="<?php echo $action1; ?>" method="post" enctype="multipart/form-data" id="form-managepro" class="form-horizontal giftnow<?php echo $customer_id; ?>">
			<div class="well">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label" for="input-mail"><?php echo $entry_to_mail; ?></label>
							<input type="text" name="email" value="<?php echo $email; ?>" readonly placeholder="<?php echo $entry_to_mail; ?>" id="input-mail" class="form-control" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label" for="input-dob"><?php echo $entry_dob; ?></label>
							<input type="text" name="date_of_birth" value="<?php echo $date_of_birth; ?>" readonly placeholder="<?php echo $entry_dob; ?>" id="input-dob" class="form-control" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label class="control-label" for="input-code"><?php echo $entry_code; ?></label>
							<input type="text" name="code" value="<?php echo $code; ?>" readonly placeholder="<?php echo $entry_code; ?>" id="input-code" class="form-control" />
						</div>
					</div>

					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label" for="input-date_start"><?php echo $entry_date_start; ?></label>
							<div class="input-group cdate">
								<input type="text" name="date_start" data-date-format="YYYY-MM-DD" placeholder="<?php echo $entry_date_start; ?>" id="input-date_start" class="form-control" />
								<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							</div>
							<div class="error_date_start"></div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label" for="input-date_end"><?php echo $entry_date_end; ?></label>
							<div class="input-group cdate1">
								<input type="text" name="date_end" data-date-format="YYYY-MM-DD" placeholder="<?php echo $entry_date_end; ?>" id="input-date_end" class="form-control" />
								<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							</div>
							<div class="error_date_end"></div>
						</div>
					</div>
					
					<div class="col-sm-4">
						<div class="form-group">
						<label class="control-label" for="input-dis_type"><?php echo $entry_dis_type; ?></label>
						  <select name="dis_type" id="input-dis_type" class="form-control">
							<?php foreach($types as $type){?>
							<?php if ($type['value'] == $tmdbirthday_dis_type){ ?>
							<option value="<?php echo $type['value']; ?>" selected="selected"><?php echo $type['text']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $type['value']; ?>"><?php echo $type['text']; ?></option>
							<?php } ?>
							<?php } ?>
						  </select>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group required">
							<label class="control-label" for="input-dis_value"><?php echo $entry_dis_value; ?></label>
							<input type="text" name="dis_value" value="<?php echo $tmdbirthday_dis_value; ?>" placeholder="<?php echo $entry_dis_value; ?>" id="input-dis_value" class="form-control" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group required">
							<label class="control-label" for="input-total"><?php echo $entry_total; ?></label>
							<input type="text" name="total_amount" value="<?php echo $tmdbirthday_total_amount; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label" for="input-use_coupon"><?php echo $entry_use_coupon; ?></label>
							<input type="text" name="use_coupon" placeholder="<?php echo $entry_use_coupon; ?>" id="input-use_coupon" class="form-control" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="control-label" for="input-use_customer"><?php echo $entry_use_customer; ?></label>
							<input type="text" name="use_customer" placeholder="<?php echo $entry_use_customer; ?>" id="input-use_customer" class="form-control" />
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-subject"><?php echo $entry_subject; ?></label>
						<div class="col-sm-10">
							<input type="text" name="subject" value="<?php echo $tmdbirthday_subject; ?>" placeholder="<?php echo $entry_subject; ?>" id="input-subject" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-message"><?php echo $entry_message; ?></label>
						<div class="col-sm-10">
							<textarea type="text" name="message" placeholder="<?php echo $entry_message; ?>" id="input-message" class="form-control summernote"><?php echo $messages; ?></textarea>
						</div>
					</div>

					<div class="col-sm-8 pull-right">
						<button type="button" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-info btn-lg tmdgiftadd" rel="<?php echo $customer_id; ?>"><?php echo $text_addgift; ?></button>
					</div>
				</div>
			</div>
		</form>
    </div>
  </div>
</div>

<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<script type="text/javascript"><!--
$('#languages a:first').tab('show');
//--></script>

<script type="text/javascript"><!--
$('.cdate').datetimepicker({
	pickTime: true
});
$('.cdate1').datetimepicker({
	pickTime: true
});
//--></script>