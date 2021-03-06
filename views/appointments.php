<?php t_startHead( 'Turnos' ); ?>

	<style>
		.table {
			margin: 0;
		}
		.table th {
			text-align: center;
		}
		.table th:first-child {
			width: 100px;
		}
		.is2-grid-wrapper {
			height: 500px;
			overflow-y: scroll;
			border: 1px solid #ccc;
			border-right: 0;
			border-top: 0;
		}
		.is2-grid-header {
			border-radius: 5px 5px 0 0;
			color: #fff;
		}
		.is2-ascdescmenu, .is2-statusmenu {
			float: right;
			padding: 0;
		}
		.is2-ascdescmenu a.dropdown-toggle,
		.is2-statusmenu a.dropdown-toggle {
			padding: 0 6px;
			display: inline-block;
		}
		.is2-statusmenu .dropdown-menu {
			right: 0;
			left: inherit;
		}

		.is2-search-trigger {
			float: right;
			margin: 0 0 10px 0;
		}
		
		.is2-doctors-listbox {
			margin: 0 0 0 10px;
		}
		.bootstrap-timepicker {
			display: inline-block;
		}
		
		tr.is2-appointment-removed * {
			text-decoration: line-through;
		}
		tr.is2-appointments-dayrow td {
			background: #f1f1f1 !important;
			font-weight: 600;
			color: #555;
			text-shadow: 0 -1px 0 #fff;
		}
		tr.is2-appointments-newrow:first-of-type {	
			display: none;
		}
		tr.is2-appointments-newrow td {	
			padding: 2px;
			background: #fbfbfb;
		}
		a.is2-appointments-newtrigger {
			text-align: right;
			display: block;
		}
		tr.is2-appointments-row td:nth-child( 3 ),
		tr.is2-appointments-row td:nth-child( 4 ) {
			width: 215px;
		}
		tr.is2-appointments-row td:last-child {
			width: 215px;
		}
		
		.alert.is2-ajax-msg {
			box-shadow: 0 1px 3px #eee;
			color: #468847;
			left: 30%;
			position: absolute;
			top: -3px;
			z-index: 100;
			opacity: .9;
		}
		
		.btn.disabled {
			width: 110px;
		}
	</style>
		
<?php t_endHead(); ?>
<?php t_startBody( $username, 'appointments'  ); ?>

		<?php t_startWrapper(); ?>
			<div class="alert alert-success is2-confirm-success is2-ajax-msg" style="display:none">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡El turno ha sido confirmado satisfactoriamente!
			</div>
			<div class="alert alert-error is2-confirm-error is2-ajax-msg" style="display:none">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡No se ha podido confirmar el turno! Vuelva a intentarlo.
			</div>
			<div class="alert alert-success is2-cancel-success is2-ajax-msg" style="display:none">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡El turno ha sido cancelado satisfactoriamente!
			</div>
			<div class="alert alert-error is2-cancel-error is2-ajax-msg" style="display:none">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡No se ha podido cancelar el turno! Vuelva a intentarlo.
			</div>
			<div class="alert alert-success is2-remove-success is2-ajax-msg" style="display:none">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡El turno ha sido borrado satisfactoriamente!
			</div>
			<div class="alert alert-error is2-remove-error is2-ajax-msg" style="display:none">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡No se ha podido borar el turno! Vuelva a intentarlo.
			</div>
			<div class="alert alert-success is2-restore-success is2-ajax-msg" style="display:none">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡El turno ha sido reiniciado satisfactoriamente!
			</div>
			<div class="alert alert-error is2-restore-error is2-ajax-msg" style="display:none">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡No se ha podido reiniciar el turno! Vuelva a intentarlo.
			</div>
			<?php if( $searchError || $searchQuickError ): ?>
			<div class="alert alert-error">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				¡No se ha podido realizar la búsqueda! Vuelva a intentarlo.
			</div>
			<?php endif; ?>
		
			<div class="is2-pagetitle clearfix">
				<h3>Turnos</h3>
				<a class="btn pull-right" href="/turnos/crear"><i class="icon-plus"></i> Crear un nuevo turno</a>
				<form class="form-search pull-right is2-search-quick-form" method="post" action="/turnos/busqueda-rapida">
					<div class="is2-search-quick-control input-append control-group <?php echo $searchQuickError ? 'error': ''; ?>">
						<input type="text" class="input-large search-query is2-search-quick-input" placeholder="Búsqueda rápida" name="keyword" value="<?php echo $quickSearchValue; ?>">
						<button type="submit" class="btn"><i class="icon-search"></i></button>
					</div>
				</form>
			</div>
			
			<div id="is2-search-appointments-wrapper" class="accordion">
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" href="#is2-search-appointments" data-parent="#is2-search-appointments-wrapper">
							Búsqueda avanzada...
						</a>
					</div>
					<div id="is2-search-appointments" class="accordion-body collapse<?php echo $searchError ? ' in ' : ' out'; ?>">
						<form class="accordion-inner" method="post" action="/turnos/busqueda-avanzada">
							<fieldset class="form-inline">
								<legend>Rango de fechas</legend>
								<div class="alert alert-info">
									Deje en blanco estos campos, si no desea buscar por rango de fechas
								</div>
								<label>Desde:
									<input type="text" class="input-small datepicker" name="fromDate" value="<?php echo __dateISOToLocale( __sanitizeValue( $persistValues['fromDate'] ) ); ?>">
								</label>
								<label>hasta:
									<input type="text" class="input-small datepicker" name="toDate" value="<?php echo __dateISOToLocale( __sanitizeValue( $persistValues['toDate'] ) ); ?>">
								</label>
							</fieldset>
							<fieldset class="form-inline">
								<legend>Rango de horas</legend>
								<div class="alert alert-info">
									Deje en blanco estos campos, si no desea buscar por rango de horas
								</div>
								<div class="bootstrap-timepicker">
									<label>Desde:
										<input type="text" class="input-mini timepicker" name="fromTime" value="<?php echo __timeISOToLocale( __sanitizeValue( $persistValues['fromTime'] ) ); ?>">
									</label>
								</div>
								<div class="bootstrap-timepicker">
									<label>hasta:
										<input type="text" class="input-mini timepicker" name="toTime" value="<?php echo __timeISOToLocale( __sanitizeValue( $persistValues['toTime'] ) ); ?>">
									</label>
								</div>
							</fieldset>
							<fieldset>
								<legend>Médicos</legend>
								<label class="radio">
									<input type="radio" name="doctorsSearch" value="all" class="is2-doctors-all" <?php echo !count( $persistValues['doctorsList'] ) ? 'checked' : ''; ?>>
									Todos
								</label>
								<label class="radio">
									<input type="radio" name="doctorsSearch" value="custom" class="is2-doctors-custom" <?php echo count( $persistValues['doctorsList'] ) ? 'checked' : ''; ?>>
									Solos los turnos que tenga asociados estos médicos...
								</label>
								<div class="is2-doctors-listbox alert alert-info">
								<?php foreach( $doctors as $doctor ): ?>
									<label class="checkbox">
										<input type="checkbox" name="doctorsList[]" value="<?php echo $doctor['id']; ?>" <?php echo isset( $persistValues['doctorsList'][$doctor['id']] ) ? 'checked' : ''; ?>>
										<?php echo $doctor['apellidos'] . ', ' . $doctor['nombres']; ?>
									</label>
								<?php endforeach; ?>
								</div>
							</fieldset>
							<fieldset>
								<legend>Pacientes</legend>
								<label class="radio">
									<input type="radio" name="patientsSearch" value="" <?php echo !$persistValues['patientsDNI'] ? 'checked' : ''; ?>>
									Todos
								</label>
								<label class="radio">
									<input type="radio" name="patientsSearch" value="custom" class="is2-patients-custom" <?php echo $persistValues['patientsDNI'] ? 'checked' : ''; ?>>
									Solos los turnos que tenga asociados estos pacientes con DNI...
								</label>
								<input class="input-xxlarge is2-patients-search" type="text" placeholder="Separe con espacios cada DNI de los pacientes" name="patientsList" value="<?php echo __sanitizeValue( $persistValues['patientsDNI'] ); ?>">
								<div class="alert alert-info">
									Si desea buscar varios pacientes a la vez, ingrese los DNI en cuestión separados por espacios, por ejemplo: <strong>7.432.211 4.533.667 7.667.888</strong> <em>(no es necesario que los escriba con puntos)</em>
								</div>
							</fieldset>
							<fieldset>
								<legend>Estado del turno</legend>
								<label class="radio">
									<input type="radio" name="status" value="" <?php echo !$persistValues['status'] ? 'checked' : ''; ?>>
									Mostrar todos los turnos
								</label>
								<label class="radio">
									<input type="radio" name="status" value="confirmados" <?php echo $persistValues['status'] == 'confirmados' ? 'checked' : ''; ?>>
									Mostrar solo los turnos que estén confirmados
								</label>
								<label class="radio">
									<input type="radio" name="status" value="cancelados" <?php echo $persistValues['status'] == 'cancelados' ? 'checked' : ''; ?>>
									Mostrar solo los turnos que estén cancelados
								</label>
							</fieldset>
							
							<button type="submit" class="btn btn-large btn-primary is2-search-trigger">Buscar</button>
						</form>
					</div>
				</div>
			</div>
			
			<?php if( $tooMuchRecords ): ?>
			<div class="alert">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				La búsqueda realizada a devuelto demasiados turnos, solo se muestran los primeros <strong>20</strong> turnos econtrados, trate de ser más específico en su criterio de búsqueda
			</div>
			<?php elseif( $currentDate ): ?>
			<div class="alert">
				Se muestran los turnos desde día presente (<strong><?php echo $currentDate; ?></strong>) hasta los próximos 7 días.
			</div>
			<?php endif; ?>
			
			<?php if( count( $appointments ) ): ?>
			<table class="table is2-grid-header btn-inverse">
				<thead>
					<tr>
						<th>
							Fecha
							<?php t_dateMenu(); ?>
						</th>
						<th>Hora</th>
						<th>Médico</th>
						<th>Paciente</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<div class="is2-grid-wrapper">
				<table class="table is2-grid">
					<tbody>
					<?php $currentDate = null; ?>
					<?php foreach( $appointments as $appointment ): ?>
						<?php if( $appointment['fecha'] != $currentDate ): ?>
							<?php $currentDate = $appointment['fecha']; ?>
							<?php $d = strtotime( $currentDate ); ?>
							<?php $dateLocale = date( 'd/m/Y', $d ); ?>
						<?php t_appointmentNewRow( date( 'd/m/Y', strtotime( $currentDate . ' previous day' ) ) ); ?>
						<tr class="is2-appointments-dayrow" data-appointment-date="<?php echo $dateLocale; ?>">
							<td><?php echo $DAYNAME[date( 'D', $d )] . ', ' . date( 'j', $d ); ?></td>
							<td><?php t_timeMenu(); ?></td>
							<td></td>
							<td></td>
							<td><?php t_statusMenu(); ?></td>
						</tr>
						<?php endif; ?>
						<?php if( $appointment['hora'] ): ?>
						<tr class="is2-appointments-row" data-appointment-id="<?php echo $appointment['id']; ?>" data-appointment-date="<?php echo $dateLocale; ?>" data-appontment-status="<?php echo $appointment['estado']; ?>">
							<td>&nbsp;</td>
							<td class="is2-appointment-time"><?php echo substr( $appointment['hora'], 0, 5 ); ?></td>
							<td><?php echo $appointment['medicoApellidos'] . ', ' .  $appointment['medicoNombres']; ?></td>
							<td><?php echo $appointment['pacienteApellidos'] . ', ' .  $appointment['pacienteNombres']; ?></td>
							<td data-appointment-id="<?php echo $appointment['id']; ?>" class="is2-appointment-status">
								<button class="btn btn-small btn-success disabled" style="display:<?php echo $appointment['estado'] == 'confirmado' ? 'inline-block' : 'none'; ?>" data-appointment-id="<?php echo $appointment['id']; ?>"><i class="icon-ok"></i> Confirmado</button>
								<button class="btn btn-small btn-warning disabled" style="display:<?php echo $appointment['estado'] == 'cancelado' ? 'inline-block' : 'none'; ?>" data-appointment-id="<?php echo $appointment['id']; ?>"><i class="icon-exclamation-sign"></i> Cancelado</button>
								<?php $isWaiting = $appointment['estado'] == 'esperando'; ?>
								<a class="btn btn-mini btn-link is2-trigger-restore" href="#is2-modal-restore" data-toggle="modal" data-appointment-id="<?php echo $appointment['id']; ?>" style="display:<?php echo !$isWaiting ? 'inline-block' : 'none'; ?>">Deshacer acción</a>
								<div style="display:<?php echo $isWaiting ? 'block' : 'none'; ?>" data-appointment-id="<?php echo $appointment['id']; ?>">
									<a class="btn btn-small is2-trigger-confirm" href="#is2-modal-confirm" data-toggle="modal" data-appointment-id="<?php echo $appointment['id']; ?>">Confirmar</a>
									<a class="btn btn-small is2-trigger-cancel" href="#is2-modal-cancel" data-toggle="modal" data-appointment-id="<?php echo $appointment['id']; ?>">Cancelar</a>
									<a class="btn btn-small btn-danger is2-trigger-remove" href="#is2-modal-remove" data-toggle="modal" data-appointment-id="<?php echo $appointment['id']; ?>">Borrar</a>
								</div>
							</td>
						</tr>
						<?php endif; ?>
					<?php endforeach; ?>
						<?php t_appointmentNewRow( $dateLocale ); ?>
					</tbody>
				</table>
			</div>
			<?php else: ?>
			<div class="alert alert-error">
				No se han encontrado turnos según el criterio de búsqueda específicado
			</div>
			<?php endif; ?>

		<?php t_endWrapper(); ?>
		
		<!-- modals -->
		<form id="is2-modal-confirm" class="modal hide fade">
			<div class="modal-body">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<span><strong>¿Estás seguro que confirmar el turno?</strong></span>
			</div>
			<div class="modal-footer">
				<span class="is2-preloader is2-preloader-bg pull-left is2-preloader-confirm"></span>
				<button class="btn is2-modal-confirm-close" data-dismiss="modal">No</button>
				<button class="btn btn-primary" type="submit">Sí</button>
			</div>
			<input type="hidden" name="id" class="is2-modal-confirm-id">
		</form>
		
		<form id="is2-modal-cancel" class="modal hide fade">
			<div class="modal-body">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<span><strong>¿Estás seguro que desea cancelar el turno?</strong></span>
			</div>
			<div class="modal-footer">
				<span class="is2-preloader is2-preloader-bg pull-left is2-preloader-cancel"></span>
				<button class="btn is2-modal-cancel-close" data-dismiss="modal">No</button>
				<button class="btn btn-primary" type="submit">Sí</button>
			</div>
			<input type="hidden" name="id" class="is2-modal-cancel-id">
		</form>
		
		<form id="is2-modal-remove" class="modal hide fade">
			<div class="modal-body">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<span><strong>¿Estás seguro que desea borrar el turno?</strong></span>
			</div>
			<div class="modal-footer">
				<span class="is2-preloader is2-preloader-bg pull-left is2-preloader-remove"></span>
				<button class="btn is2-modal-remove-close" data-dismiss="modal">No</button>
				<button class="btn btn-primary" type="submit">Sí</button>
			</div>
			<input type="hidden" name="id" class="is2-modal-remove-id">
		</form>
		
		<form id="is2-modal-restore" class="modal hide fade">
			<div class="modal-body">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<span><strong>¿Estás seguro que desea que el turno vuelva a su estado original?</strong></span>
			</div>
			<div class="modal-footer">
				<span class="is2-preloader is2-preloader-bg pull-left is2-preloader-restore"></span>
				<button class="btn is2-modal-restore-close" data-dismiss="modal">No</button>
				<button class="btn btn-primary" type="submit">Sí</button>
			</div>
			<input type="hidden" name="id" class="is2-modal-restore-id">
		</form>
		
<?php t_endBody(); ?>

<script>
(function() {
// *** ACA PARA CUANDO MUESTRO LOS MODALS *** //
	$( '.is2-grid' ).delegate( '.is2-trigger-confirm', 'click', function( e ) {
		// hay que poner el turno id en input hidden
		$( '#is2-modal-confirm input' ).val( $( this ).attr( 'data-appointment-id' ) );
	
	} ).delegate( '.is2-trigger-cancel', 'click', function( e ) {
		$( '#is2-modal-cancel input' ).val( $( this ).attr( 'data-appointment-id' ) );
	
	} ).delegate( '.is2-trigger-remove', 'click', function( e ) {
		$( '#is2-modal-remove input' ).val( $( this ).attr( 'data-appointment-id' ) );
		
	} ).delegate( '.is2-trigger-restore', 'click', function( e ) {
		$( '#is2-modal-restore input' ).val( $( this ).attr( 'data-appointment-id' ) );
	} );
	
// *** ACA PARA CUANDO SORTEO LA GRID *** //
	// POR FECHA
	$( '.is2-date .is2-trigger-orderby' ).on( 'click', function( e ) {
		// dont append the #
		e.preventDefault();
		var $el = $( this ),
			orderBy = $el.attr( 'data-orderby' ),
			res = getQueryString();
			
		window.location = '/turnos?' + ( res.length ? res.join( '&' ) + '&' : '' ) + 'fecha=' + orderBy;
	} );

	// LO NECESIT PARA ORDERNAR POR HORA
	var BinaryTree = function() {
	};
	BinaryTree.prototype = {
		add: function( data ) {
			if( !this.key ) {
				this.key = data.key;
				// es un array por el tema de los repetidos
				this.data = [ data.data ];
				this.left = null;
				this.right = null;
				
			} else if( this.key > data.key ) {
				if( !this.left ) { 
					this.left = new BinaryTree();
				}
				this.left.add( data );
				
			} else if( this.key < data.key ) {
				if( !this.right ) {
					this.right = new BinaryTree();
				}
				this.right.add( data );
				
			} else {
				// repetidos cuentan
				this.data.push( data.data );
			}
		},
		walkAsc: function( callback ) {
			if( this.key ) {
				this.walkAsc.call( this.right, callback );
				while( this.data.length ) {
					callback( this.data.shift() );
				}
				this.walkAsc.call( this.left, callback );
			}
		},
		walkDesc: function( callback ) {
			if( this.key ) {
				this.walkDesc.call( this.left, callback );
				while( this.data.length ) {
					callback( this.data.shift() );
				}
				this.walkDesc.call( this.right, callback );
			}
		}
	};
	
	// POR HORA
	$( '.is2-grid' ).delegate( '.is2-time .is2-trigger-orderby', 'click', function( e ) {
		e.preventDefault();
		var $el = $( this ),
			orderBy = $el.attr( 'data-orderby' ),
			$row = $el,
			$cells;
			
		var reorderRows = function( $el ) {
			$row = $row.after( $el );
		};

		while( ( $row = $row.parent() ).length && !$row.hasClass( 'is2-appointments-dayrow' ) );
		
		var $cells = $( 'tr[data-appointment-date="' + $row.attr( 'data-appointment-date' ) + '"]:not( :first ) td.is2-appointment-time' ),
			i = 0, l = $cells.length,
			times = [], time,
			tree = new BinaryTree();
			
		if( l > 1 ) {
			for( ; i < l; i++ ) {
				$cell = $cells.eq( i );
				time = $cell.html().replace( ':', '' ) - 0;
				tree.add( { key: time, data: $cell.parent() } );
			}

			if( orderBy === 'asc' ) {
				tree.walkAsc( reorderRows );
			} else {
				tree.walkDesc( reorderRows );
			}
		}
	
	} );
	
	// POR ESTADO
	$( '.is2-grid' ).delegate( '.is2-trigger-status', 'click', function( e ) {
		e.preventDefault();
		var $el = $( this ),
			fieldValue = $el.attr( 'data-field-value' ),
			$target = $el,
			$cells;
			
		while( ( $target = $target.parent() ).length && !$target.hasClass( 'is2-appointments-dayrow' ) );

		var $rows =$( 'tr[data-appointment-date="' + $target.attr( 'data-appointment-date' ) + '"]:not( :first )' ), $row,
			i = 0, l = $rows.length,
			rows = [];
	
		if( l > 1 ) {
			for( ; i < l; i++ ) {
				$row = $rows.eq( i );
				if( $row.attr( 'data-appointment-status' ) === fieldValue ) {
					rows.splice( 0, 0, $row );
				} else {
					rows.push( $row );
				}
			}
			while( rows.length ) {
				$target = $target.after( rows.shift() );
			}
		}
	} );
	
	var getQueryString = function() {
		// tiro un redirect
		var queryString = window.location.search.replace( /^\?/, '' ),
			segs = queryString ? queryString.split( '&' ) : [], seg,
			pat = /(?:fecha=(?:asc|desc)|(?:exito|error)=[^$]+)/,
			res = [];
		
		while( segs.length ) {
			seg = segs.shift();
			if( !pat.test( seg ) ) {
				res.push( seg );
			}
		}
		
		return res;
	};
	
// *** ACA PARA LA BUSQUEDA DE TURNOS *** //
	$( '.datepicker' ).datepicker( {
		format: 'dd/mm/yyyy',
		language: 'es'
	} );
	$( '.timepicker' ).timepicker( {
		showInputs: false,
		defaultTime: false,
		showMeridian: false
	});
	$( '.is2-doctors-listbox' ).on( 'click', function( e ) {
		$( '.is2-doctors-custom' ).click();
	} );
	$( '.is2-patients-search' ).on( 'click', function( e ) {
		$( '.is2-patients-custom' ).click();
	} );
	
// *** BUSQUEDA RAPIDA *** //
	var $searchQuery = $( '.is2-search-quick-input' );
	var $searchControlGroup = $( '.is2-search-quick-control' );
	$( '.is2-search-quick-form' ).on( 'submit', function( e ) {
		var keyword = $searchQuery.val().trim();
		if( !keyword ) {
			e.preventDefault();
			$searchControlGroup.addClass( 'error' );
			return;
		}
		$searchControlGroup.removeClass( 'error' );
	} );
	
// *** APPOINTMENTS AJAX FUNCIONALITY *** //
	var showAppointmentAction = function( id, type ) {
	
		var $row = $( 'tr[data-appointment-id=' + id + ']' ),
			status;
	
		$( 'td:last-child[data-appointment-id=' + id + '] > *' ).hide();
	
		if( type === 'restore' ) {
			$( 'div[data-appointment-id=' + id + ']' ).show();
			status = 'esperando';
			
		} else if( type === 'confirm' ) {
			$( 'button.btn-success[data-appointment-id=' + id + ']' ).show();
			$( '.is2-trigger-restore[data-appointment-id=' + id + ']' ).show();
			status = 'confirmado';
			
		} else if( type === 'cancel' ) {
			$( 'button.btn-warning[data-appointment-id=' + id + ']' ).show();
			$( '.is2-trigger-restore[data-appointment-id=' + id + ']' ).show();
			status = 'cancelado';
		
		} else if( type === 'remove' ) {
			$row.addClass( 'is2-appointment-removed' );
			status = 'borrado';
		}
		
		$row.effect( 'highlight', null, 1500 );
		$row.attr( 'data-appointment-status', status );
	};
	
	var AppointmentActionAjax = function( type, url ) {
		this.type = type;
		this.$preloader = $( '.is2-preloader-' + type );
		this.url = url;
		this.$id = $( '.is2-modal-' + type + '-id' );
		this.$closeModal = $( '.is2-modal-' + type + '-close' );
		this.$successMsg = $( '.is2-' + type + '-success' );
		this.$errorMsg = $( '.is2-' + type + '-error' );
		$( '#is2-modal-' + type ).on( 'submit', $.proxy( this.send, this ) );
	};
	AppointmentActionAjax.isWaiting = false;
	AppointmentActionAjax.$allMsgs = $( '.is2-ajax-msg' );
	AppointmentActionAjax.prototype = {
	
		send: function( e ) {
			e.preventDefault();
			
			if( AppointmentActionAjax.isWaiting ) {
				return false;
			}
			AppointmentActionAjax.isWaiting = true;
			this.$preloader.css( 'visibility', 'visible' );
			
			$.ajax( {
				url: this.url,
				data: {
					id: this.$id.val()
				},
				type: 'POST',
				dataType: 'json',
				success: this.response,
				error: this.response,
				context: this
			} );
		},
		
		response: function( dataResponse ) {
			AppointmentActionAjax.isWaiting = false;
			this.$preloader.css( 'visibility', 'hidden' );
			this.$closeModal.click();
			
			AppointmentActionAjax.$allMsgs.hide();
			
			var $msg;
			if( !dataResponse.success ) {
				$msg = this.$errorMsg;
				return;
			}
			$msg = this.$successMsg;
			$msg.css( 'top', -40 ).show().animate( { top: '+=36' }, { complete: function() {
				$msg.delay( 2000 ).animate( { top: '-=44' } );
			} } );
			
			showAppointmentAction( dataResponse.data.id, this.type );
		}
	};
	
	new AppointmentActionAjax( 'confirm', '/turnos/confirmar' );
	new AppointmentActionAjax( 'cancel', '/turnos/cancelar' );
	new AppointmentActionAjax( 'remove', '/turnos/borrar' );
	new AppointmentActionAjax( 'restore', '/turnos/reiniciar' );

})();
</script>