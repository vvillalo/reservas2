var rootURL = "http://solweb.co/reservas/api/login/logins";

var currentLogin;

 
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
	newLogin();
	return false;
});

$('#btnSave').click(function() {
	if ($('#loginId').val() == '')
		addLogin();
	else
		updateLogin();
	return false;
});

$('#btnDelete').click(function() {
	deleteLogin();
	return false;
});

$('#loginList a').live('click', function() {
	findById($(this).data('identity'));
});


function search(searchKey) {
	if (searchKey == '') 
		findAll();
	else
		findByName(searchKey);
}

function newLogin() {
	$('#btnDelete').hide();
	currentLogin = {};
	renderDetails(currentLogin);
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
			console.log('findById success: ' + data.user);
			currentLogin = data;
			renderDetails(currentLogin);
		}
	});
}

function addLogin() {
	console.log('addLogin');
	$.ajax({
		type: 'POST',
		contentType: 'application/json',
		url: rootURL,
		dataType: "json",
		data: formToJSON(),
		success: function(data, textStatus, jqXHR){
			alert('Login created successfully');
			$('#btnDelete').show();
			$('#loginId').val(data.id);
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('addLogin error: ' + textStatus);
		}
	});
}

function updateLogin() {
	console.log('updateLogin');
	$.ajax({
		type: 'PUT',
		contentType: 'application/json',
		url: rootURL + '/' + $('#loginId').val(),
		dataType: "json",
		data: formToJSON(),
		success: function(data, textStatus, jqXHR){
			alert('Login updated successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('updateLogin error: ' + textStatus);
		}
	});
}

function deleteLogin() {
	console.log('deleteLogin');
	$.ajax({
		type: 'DELETE',
		url: rootURL + '/' + $('#loginId').val(),
		success: function(data, textStatus, jqXHR){
			alert('Login deleted successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('deleteLogin error');
		}
	});
}

function renderList(data) {
	
	var list = data == null ? [] : (data.login instanceof Array ? data.login : [data.login]);

	$('#loginList li').remove();
	$.each(list, function(index, login) {
		$('#loginList').append('<li><a href="#" data-identity="' + login.id + '">'+login.name+'</a></li>');
	});
}

function renderDetails(login) {
	$('#loginId').val(login.id);
	$('#user').val(login.user);
	$('#pass').val(login.pass);
}

function formToJSON() {
	return JSON.stringify({
		"id": $('#loginId').val(), 
		"user": $('#user').val(), 
		"pass": $('#pass').val()
		});
}
