<?php
include_once ('../include.php');
?>
</head>
<script type="text/javascript">
$(document).ready(function() {
    $('[data-field]').not('.tile-search').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
});
// function saveField(field) {
//     if(field.type == 'change') {
//         field = this;
//     }
//     var field_name = $(field).data('field');
//     var table_name = $(field).data('table');
//     if(field_name != '' && table_name != '' && field.value != '*NEW_VALUE*') {
//         $(field).nextAll('input').first().hide();
//         $(field).nextAll('div.newfield').hide();
//         var field_info = new FormData();
//         field_info.append('tile_name', '<?= FOLDER_NAME ?>');
//         field_info.append('field', field_name);
//         field_info.append('table', table_name);
//         field_info.append('contactid', $('[name=contactid]').val());
//         if($(field).data('row-field') != undefined) {
//             field_info.append('row_field', $(field).data('row-field'));
//             field_info.append('row_id', $(field).data('row-id'));
//         }
//         if(field.name.substr(-2) == '[]') {
//             var array_values = [];
//             $('[name^="'+field.name.slice(0,-2)+'"]').each(function() {
//                 array_values.push(this.value);
//             });
//             // var array_values = $('[name^="'+field.name.slice(0,-2)+'"]').map(function() {debugger;
//             //  return this.value;
//             // }).get();
//             field_info.append('value', JSON.stringify(array_values));
//             if($(field).data('delimiter') != undefined) {
//                 field_info.append('delimiter', $(field).data('delimiter'));
//             } else {
//                 field_info.append('delimiter', ',');
//             }
//         } else {
//             field_info.append('value', ($(field).data('value') == undefined ? $(field).val() : $(field).data('value')));
//         }
//         if($(field).data('contactid-field') != undefined) {
//             field_info.append('contactid_field', $(field).data('contactid-field'));
//         }
//         if($(field).data('contactid-category-field') != undefined) {
//             field_info.append('contactid_category_field', $(field).data('contactid-category-field'));
//         }
//         if($(field).data('contact-category') != undefined) {
//             field_info.append('contact_category', $(field).data('contact-category'));
//         }
//         if($(field).data('replicating-fieldname') != undefined) {
//             field_info.append('replicating_fieldname', $(field).data('replicating-fieldname'));
//         }
//         if($(field).data('contact-id') != undefined) {
//             field_info.append('contact_id', $(field).val());
//         }
//         if($(field).data('new-category') != undefined) {
//             field_info.append('new_category', $(field).val());
//         }
//         if($(field).data('new-firstname') != undefined) {
//             field_info.append('new_first_name', $(field).val());
//         }
//         if($(field).data('new-lastname') != undefined) {
//             field_info.append('new_last_name', $(field).val());
//         }
//         if($(field).data('no-contactid') != undefined) {
//             field_info.append('no_contactid', $(field).data('no-contactid'));
//         }
//         var label = $(field).closest('.form-group').find('label').first().text();
//         if(label.substring(label.length - 1) == ':') {
//             label = label.substring(0,label.length - 1);
//         }
//         field_info.append('label', label);
//         var ajax_data = {
//             processData: false,
//             contentType: false,
//             url: '../Contacts/contacts_ajax.php?action=contact_values',
//             method: 'POST',
//             data: field_info,
//             success: function(response) {
//                 console.log(response);
//                 if($(field).data('row-field') != undefined) {
//                     $(field).closest('form').find('input,select,textarea').each(function() {
//                         if($(this).data('row-id') == '' && $(this).data('table') == $(field).data('table')) {
//                             $(this).data('row-id', response);
//                         }
//                     });
//                     if(field.type == 'file') {
//                         $(field).prevAll('li:contains("Uploading file...")').last().remove();
//                         $(field).before('<li><a href="download/'+$(field)[0].files[0].name+'" target="_blank">'+$(field)[0].files[0].name+'</a></li>');
//                         $(field).val('');
//                     }
//                 } else if(field.type == '') {
//                     $(field).closest('span').remove();
//                 } else if(field.type == 'file') {
//                     $(field).prevAll('li:contains("Uploading file...")').last().remove();
//                     $(field).before('<li><a href="download/'+$(field)[0].files[0].name+'" target="_blank">'+$(field)[0].files[0].name+'</a></li>');
//                     $(field).val('');
//                 } else if(field.name == 'a_label') {
//                     $(field).prevAll('a').first().text(field.value);
//                     $(field).hide();
//                     $(field).val('');
//                 } else if(field.name == 'category') {
//                     if($('[name=contactid]').val() == '' || $('[name=contactid]').val() == 'new') {
//                         window.location.replace('?profile=false&category='+field.value+'&edit='+response);
//                     } else {
//                         window.location.replace('?profile=false&category='+field.value+'&edit='+ $('[name=contactid]').val());
//                     }
//                 }
//             }
//         };
//         if(field.type == 'file') {
//             ajax_data.data.append('value', 'upload');
//             ajax_data.data.append('file', $(field)[0].files[0]);
//             $(field).before('<li>Uploading file...</li>');
//         }
//         $.ajax(ajax_data);
//     } else if(field.value == '*NEW_VALUE*') {
//         $(field).hide();
//         $(field).nextAll('input').first().show().focus();
//         $(field).nextAll('div.newfield').show();
//     }
// }

</script>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('medication');
if(!empty($_POST['search_clientid'])) {
    $_GET['edit'] = $_POST['search_clientid'];
}
?>
<div class="container">
  <div class="row">

    <h1>MAR Sheet - <?= get_contact($dbc, $_GET['edit']) ?></h1>
	<div class="pad-left gap-top double-gap-bottom">
        <a href="../Medication/medication.php" class="btn config-btn">Back to Dashboard</a>
    </div>

    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        <?php 
            include('../Medication/marsheet_inc.php');
        ?>
    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
