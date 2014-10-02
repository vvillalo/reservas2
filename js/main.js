var rootURL = "http://solweb.co/reservas/api/reservations";

var currentReservation;

 
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
	newReservation();
	return false;
});

$('#btnSave').click(function() {
	if ($('#reservationId').val() == '')
		addReservation();
	else
		updateReservation();
	return false;
});

$('#btnDelete').click(function() {
	deleteReservation();
	return false;
});

$('#reservationList a').live('click', function() {
	findById($(this).data('identity'));
});


function search(searchKey) {
	if (searchKey == '') 
		findAll();
	else
		findByName(searchKey);
}

function newReservation() {
	$('#btnDelete').hide();
	currentReservation = {};
	renderDetails(currentReservation);
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
			currentReservation = data;
			renderDetails(currentReservation);
		}
	});
}

function addReservation() {
	console.log('addReservation');
	$.ajax({
		type: 'POST',
		contentType: 'application/json',
		url: rootURL,
		dataType: "json",
		data: formToJSON(),
		success: function(data, textStatus, jqXHR){
			alert('Reservation created successfully');
			$('#btnDelete').show();
			$('#reservationId').val(data.id);
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('addReservation error: ' + textStatus);
		}
	});
}

function updateReservation() {
	console.log('updateReservation');
	$.ajax({
		type: 'PUT',
		contentType: 'application/json',
		url: rootURL + '/' + $('#reservationId').val(),
		dataType: "json",
		data: formToJSON(),
		success: function(data, textStatus, jqXHR){
			alert('Reservation updated successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('updateReservation error: ' + textStatus);
		}
	});
}

function deleteReservation() {
	console.log('deleteReservation');
	$.ajax({
		type: 'DELETE',
		url: rootURL + '/' + $('#reservationId').val(),
		success: function(data, textStatus, jqXHR){
			alert('Reservation deleted successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('deleteReservation error');
		}
	});
}

function renderList(data) {
	
	var list = data == null ? [] : (data.reservation instanceof Array ? data.reservation : [data.reservation]);

	$('#reservationList li').remove();
	$.each(list, function(index, reservation) {
		$('#reservationList').append('<li><a href="#" data-identity="' + reservation.id + '">'+reservation.name+'</a></li>');
	});
}

function renderDetails(reservation) {
	$('#reservationId').val(reservation.id);
	$('#name').val(reservation.name);
	$('#hour').val(reservation.hour);
	$('#day').val(reservation.day);
	$('#month').val(reservation.month);
	$('#year').val(reservation.year);
	$('#description').val(reservation.description);
}

function formToJSON() {
	return JSON.stringify({
		"id": $('#reservationId').val(), 
		"name": $('#name').val(), 
		"hour": $('#hour').val(),
		"day": $('#day').val(),
		"month": $('#month').val(),
		"year": $('#year').val(),
		"description": $('#description').val()
		});
}
