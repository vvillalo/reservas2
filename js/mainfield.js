var rootURL = "http://solweb.co/reservas/api/field/fields";

var currentField;

 
findAll();


$('#btnDelete').hide();


$('#btnSearch').click(function() {
	search($('#searchKey').val());
	return false;
});


$('#searchKey').keypress(function(e){
	if(e.which == 13) {
		search($('#searchKey').val());
		e.preventDefault();
		return false;
    }
});

$('#btnAdd').click(function() {
	newField();
	return false;
});

$('#btnSave').click(function() {
	if ($('#fieldId').val() == '')
		addField();
	else
		updateField();
	return false;
});

$('#btnDelete').click(function() {
	deleteField();
	return false;
});

$('#fieldList a').live('click', function() {
	findById($(this).data('identity'));
});


function search(searchKey) {
	if (searchKey == '') 
		findAll();
	else
		findByName(searchKey);
}

function newField() {
	$('#btnDelete').hide();
	currentField = {};
	renderDetails(currentField);
}

function findAll() {
	console.log('findAll');
	$.ajax({
		type: 'GET',
		url: rootURL,
		dataType: "json", 
		success: renderList
	});
}

function findByName(searchKey) {
	console.log('findByName: ' + searchKey);
	$.ajax({
		type: 'GET',
		url: rootURL + '/search/' + searchKey,
		dataType: "json",
		success: renderList 
	});
}

function findById(id) {
	console.log('findById: ' + id);
	$.ajax({
		type: 'GET',
		url: rootURL + '/' + id,
		dataType: "json",
		success: function(data){
			$('#btnDelete').show();
			console.log('findById success: ' + data.name);
			currentField = data;
			renderDetails(currentField);
		}
	});
}

function addField() {
	console.log('addField');
	$.ajax({
		type: 'POST',
		contentType: 'application/json',
		url: rootURL,
		dataType: "json",
		data: formToJSON(),
		success: function(data, textStatus, jqXHR){
			alert('Field created successfully');
			$('#btnDelete').show();
			$('#fieldId').val(data.id);
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('addField error: ' + textStatus);
		}
	});
}

function updateField() {
	console.log('updateField');
	$.ajax({
		type: 'PUT',
		contentType: 'application/json',
		url: rootURL + '/' + $('#fieldId').val(),
		dataType: "json",
		data: formToJSON(),
		success: function(data, textStatus, jqXHR){
			alert('Field updated successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('updateField error: ' + textStatus);
		}
	});
}

function deleteField() {
	console.log('deleteField');
	$.ajax({
		type: 'DELETE',
		url: rootURL + '/' + $('#fieldId').val(),
		success: function(data, textStatus, jqXHR){
			alert('Field deleted successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('deleteField error');
		}
	});
}

function renderList(data) {
	
	var list = data == null ? [] : (data.field instanceof Array ? data.field : [data.field]);

	$('#fieldList li').remove();
	$.each(list, function(index, field) {
		$('#fieldList').append('<li><a href="#" data-identity="' + field.id + '">'+field.name+'</a></li>');
	});
}

function renderDetails(field) {
	$('#fieldId').val(field.id);
        $('#name').val(field.name);
	$('#latitude').val(field.latitude);
	$('#length').val(field.length);
	$('#url').val(field.url);
	$('#icon').val(field.icon);
	$('#phone').val(field.phone);
        $('#address').val(field.address);
	$('#description').val(field.description);
}

function formToJSON() {
	return JSON.stringify({
		"id": $('#fieldId').val(), 
		"name": $('#name').val(), 
		"latitude": $('#latitude').val(),
		"length": $('#length').val(),
		"url": $('#url').val(),
		"icon": $('#icon').val(),
		"phone": $('#phone').val(),
                "address": $('#address').val(),
		"description": $('#description').val()
		});
}


