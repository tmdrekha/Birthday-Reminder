<div class="calendar" id="tmddates">
<input type="hidden" value="" name="tmddate" data-date-format="YYYY-MM-DD" id="dateHidden" class="tmddates">
</div>

<script type="text/javascript">
var onlyThisDates = [<?php echo $newdate; ?>];
var dateToday = new Date();

$('.calendar').datepicker({
  //startDate : dateToday,
  altField: "#dateHidden",
  altField: "#dateHiddendate",
  beforeShowDay: function (date) {
    var dt_ddmmyyyy = date.getDate() + '-' + (date.getMonth() + 1);
    if (onlyThisDates.indexOf(dt_ddmmyyyy) != -1) {
        return {
            tooltip: 'Available',
            classes: 'active'
        };
    } else {
        return false;
    }
  }

});

$('input[name=\'tmddate\']').on('change', function() {
  $(".tmdcalendar").show();
  var  new_date =$('#dateHidden').val();
  $('#dateHiddendate').val(new_date);
  if(new_date!='') {

    $.ajax({
      url: 'index.php?route=extension/module/tmdbirthday/allcustomer&token=<?php echo $token; ?>&date_of_birth='+new_date,
      type: 'post',
      data: '',
      dataType: 'json',
      beforeSend: function() {

      },
      complete: function() {
      },
      success: function(json) {
        html='';
        if (json['customers'] && json['customers'] != '') {
          for (i = 0; i < json['customers'].length; i++) {
            html+='<tr>';
              html+='<td class="text-left bgc2">' + json['customers'][i]['apply'] + '</td>';
              html+='<td class="text-left bgc2">' + json['customers'][i]['name'] + '</td>';
              html+='<td class="text-left bgc2">' + json['customers'][i]['email'] + '</td>';
              html+='<td class="text-left bgc2"><span class="label label-info">' + json['customers'][i]['date_of_birth'] + '</span></td>';
              html+='<td class="text-left bgc2">' + json['customers'][i]['customer_group'] + '</td>';
              html+='<td class="text-left bgc2">' + json['customers'][i]['status'] + '</td>';
              html+='<td class="text-left bgc2">' + json['customers'][i]['date_added'] + '</td>';
              html+='<td class="text-right bgc2">';
              html+='<a href="' + json['customers'][i]['edit'] + '" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a> <a class="btn btn-info tmdcustomer" data-toggle="modal" data-target="#myModal" rel="' + json['customers'][i]['customer_id'] + '" href="' + json['customers'][i]['href'] + '"><i class="fa fa-calendar"></i></a>';
            html+='</td></tr>';
          }
        } else {
          html+='<tr>';
          html+='<td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>';
          html+='</tr>';
        }
        $('.allcustomers').html(html);
      },
    });
  } else {
    alert('No Available For Date Of Birthday');
  }
});
</script>

<style>
.bgc2{background: #f6f6f6;color: #88908a;padding: 12px;font-weight:bold;}
.modal-header .close {
  left: 30px;
  top: -50px;
  background: #686868 none repeat scroll 0 0;
  color: #fff;
  padding: 4px;
  position: relative;
  opacity: 1;
  border: solid 3px #fff;
  border-radius: 22px;
  width: 33px;
  font-size: 17px;
}

#tmddates .table-condensed {
  width: 1037px;
  height: 460px;
}

#tmddates .dow{text-transform:uppercase;font-size: 15px;color:red;}
#tmddates .datepicker-switch{text-transform:uppercase;font-size: 15px;color:red;}
#tmddates .table-condensed th{border:2px solid #a29e9e;}
#tmddates .table-condensed td{border:2px solid #a29e9e;}

#tmddates .day{color:#0c0c0c;font-size: 15px;}

@media(max-width:768px){
  #form_booking .datepicker, .table-condensed{
    width: 100% !important;
  }
  .modal-header .close{
    left: 15px;
  }
}

.calendar .day{
  background-color : #105206;
  color : #fff;
}

#tmddates .table-condensed td .day .active {
  background-color : #05791e;
  border-color: #05791e;
  border-radius: 40px;
}
</style>