<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Personal Schedule Tracker</title>
	 <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Incluindo o arquivo CSS externo -->
</head>
<body>
<div class="container mt-5">
	{{--For search --}}
	<div class="row">
		<div class="col-md-6">
			<div class="input-group mb-3">
				<input type="text" id="searchInput" class="form-control" placeholder="Search events">
				<div class="input-group-append">
					<button id="searchButton" class="btn btn-primary">Search</button>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="btn-group mb-3" role="group" arial-label="Calendar Actions">
				<button id="exportButton" class="btn btn-success">Export Calendar</button>
				
			</div>

			 <div class="btn-group mb-3" role="group" aria-label="Calendar Actions">
                    <a href="{{ URL('add-schedule') }}" class="btn btn-success">{{__('Add')}}</a>
                </div>
		</div>
	</div>
	<div class="card">
		<div class="card-body py-6 mb-3">
			<div id="calendar"></div>
		</div>
	</div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script type="text/javascript">
	 $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

	var calendarE1 = document.getElementById('calendar');
	var events = [];
	var calendar = new FullCalendar.Calendar(calendarE1,{
		headerToolbar :{
			  left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
		},
		 initialView: 'dayGridMonth',
            timeZone: 'UTC',
            events: '/events',
            editable: true,

            //Deleting The Event
            eventContent: function(info){
            var eventTitle = info.event.title;
            var eventElement = document.createElement('div');
            eventElement.innerHTML = '<span style = "cursor:pointer;color:red;font-size:20px"> X </span>' + eventTitle;

            eventElement.querySelector('span').addEventListener('click',function(){
            	if(confirm("Are you sure you want to delete this event?")){
            		var eventId = info.event.id;
            		$.ajax({
            			method: 'DELETE',
            			url : '/schedule/'+eventId,
            			success:function(response){
            				console.log('event deleted');
            				calendar.refetchEvents();//Refresh Events
            			},
            			error:function(error){
            				console.log('error deleting event',error)
            			}
            		});
            	}
            });
            return{
            	domNodes:[eventElement]
            };
           },
           //Drag And Drop

           eventDrop:function(info){
           	var eventId= info.event.id;
           	var newStartDate = info.event.start;
           	var newEndDate = info.event.end || newStartDate;
           	var newStartDateUTC = newStartDate.toISOString().slice(0,10);
           	var newEndDateUTC = newEndDate.toISOString().slice(0,10);
           	console.log(newStartDate);

           	$.ajax({
           		method: 'PUT',
           		url:`/schedule/${eventId}`,
           		data: {
           			start_date: newStartDateUTC,
           			end_date: newEndDateUTC,

           		},
           		succes:function(){
           			console.log('event moved');
           		},
           		error:function(error){
           			console.log('error',error);
           		}

           	});
           },

           eventResize: function(info){
		var eventId = info.event.id;
		var newEndDate = info.event.end;
		var newEndDateUTC = newEndDate.toISOString().slice(0,10);

		$.ajax({
			method: 'POST',
			url: `/schedule/${eventId}/resize`,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				end_date: newEndDateUTC
			},
			success:function(){
				console.log('Event Resized success');
			},
			error:function(error){
				console.log('Error Resizing',error);
			}
		});

			},

	});

	//Event Resize

	
	calendar.render();
	
	document.getElementById('searchButton').addEventListener('click',function(){
		var searchKeywords = document.getElementById('searchInput').value.toLowerCase();
		filterAndDisplayEvents(searchKeywords);
	});

	function filterAndDisplayEvents(searchKeywords){
		$.ajax({
			method: 'GET',
			url: `/events/search?title=${searchKeywords}`,
			success:function(response){
				calendar.removeAllEvents();
				calendar.addEventSource(response);
			},

			error:function(error){
				console.log('Error searching:',error);
			},
		});
	}

	//exporting Function

	document.getElementById('exportButton').addEventListener('click',function(){
		var events = calendar.getEvents().map(function(event){
			return {
            title: event.title,
            start: event.start ? event.start.toISOString():null,
            end: event.end ? event.end.toISOString():null,
            color: event.backgroundColor,

			};
		});
        var wb = XLSX.utils.book_new();

        var ws =XLSX.utils.json_to_sheet(events);

        XLSX.utils.book_append_sheet(wb,ws,'Events');
        var arrayBuffer = XLSX.write(wb,{
            bookType: 'xlsx',
            type: 'array'
        });
        var blob = new Blob([arrayBuffer],{
           type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });

        var donwloadLink = document.createElement('a');
        donwloadLink.href = URL.createObjectURL(blob);
        donwloadLink.download= 'event.xlsx';
        donwloadLink.click();
	});
</script>
</body>
</html>