
(function ($, window) {
    'use strict';

    var MultiModal = function (element) {
        this.$element = $(element);
        this.modalCount = 0;
    };

    MultiModal.BASE_ZINDEX = 1040;

    MultiModal.prototype.show = function (target) {
        var that = this;
        var $target = $(target);
        var modalIndex = that.modalCount++;

        $target.css('z-index', MultiModal.BASE_ZINDEX + (modalIndex * 20) + 10);

        // Bootstrap triggers the show event at the beginning of the show function and before
        // the modal backdrop element has been created. The timeout here allows the modal
        // show function to complete, after which the modal backdrop will have been created
        // and appended to the DOM.
        window.setTimeout(function () {
            // we only want one backdrop; hide any extras
            if (modalIndex > 0)
                $('.modal-backdrop').not(':first').addClass('hidden');

            that.adjustBackdrop();
        });
    };

    MultiModal.prototype.hidden = function (target) {
        this.modalCount--;

        if (this.modalCount) {
            this.adjustBackdrop();
            // bootstrap removes the modal-open class when a modal is closed; add it back
            $('body').addClass('modal-open');
        }
    };

    MultiModal.prototype.adjustBackdrop = function () {
        var modalIndex = this.modalCount - 1;
        $('.modal-backdrop:first').css('z-index', MultiModal.BASE_ZINDEX + (modalIndex * 20));
    };

    function Plugin(method, target) {
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('multi-modal-plugin');

            if (!data)
                $this.data('multi-modal-plugin', (data = new MultiModal(this)));

            if (method)
                data[method](target);
        });
    }

    $.fn.multiModal = Plugin;
    $.fn.multiModal.Constructor = MultiModal;

    $(document).on('show.bs.modal', function (e) {
        $(document).multiModal('show', e.target);
    });

    $(document).on('hidden.bs.modal', function (e) {
        $(document).multiModal('hidden', e.target);
    });
}(jQuery, window));


document.addEventListener('DOMContentLoaded', () => {
    /* agregar boton a dt-buttons */
    let estado = "";

    $('#ExportButton').click(function () {
        table.rows({
            search: 'applied'
        }).data().each(function (value, index) {
            console.log(value, index);
        });
    });
    var tableuser = $('#users-table').DataTable({

        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "/users-table",
            "type": "GET"
        },
        "async": false,

        "pageLength": 10,
        "error": function (xhr, error, thrown) {
            console.log("Error en la solicitud AJAX:", error);
        },
        "language": {
            url: 'build/language/es-ES.json',
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "name"
        },
        {
            "data": "dui"
        },
        {
            orderable: false,
            className: 'action-buttons',
            render: function (data, type, row) {
                return '<div class=""><a class="btn btn-primary updateUser"  data-id="' + row.id + '">' +
                    '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>' +
                    '</a>'
                    ;
            }
        }
        ],

    });


    var table = $('#expedientes-table').DataTable({
        "lengthMenu": [
            [10, 25, 50, 75, 100],
            [10, 25, 50, 75, 100]
        ],
        "dom": '<"top"Bifpl<"clear">>rt<"bottom"ip<"clear">>',
        "buttons": [{
            className: 'pdf-button',
            id: 'ExportButton',
            text: 'Exportar PDF <i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
            action: function (e, dt, node, config) {

                // Obtener el valor actual del cuadro de búsqueda
                var searchValue = dt.search();
                // Enviar el valor de búsqueda al servidor junto con los datos exportados

                var searchvaluevacio = searchValue.trim();
                mostrarMensajeCargando();
                $.ajax({
                    url: `/exportarpdf?estado=${estado}`,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        searchData: searchvaluevacio,
                    }),
                    success: function (response) {

                        if (response.error) {
                            ocultarMensajeCargando();
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Debes tener al menos un resultado para poder generar el PDF!",
                            });
                        } else {
                            // Analizar el JSON para convertirlo en un objeto JavaScript





                            const expedientesData = JSON.parse(response).expedientes;
                            const headers = Object.keys(expedientesData[0]);

                            headers.splice(11, 1);

                            const expedientekey = Object.values(expedientesData);

                            const expedientesBody = expedientekey.map((expediente) => {
                                const { Estado, "Fecha Archivado": fechaArchivado, ...resto } = expediente;

                                return {
                                    ...resto,
                                    Estado: Estado == 0 ? 'EN TRAMITE' : `ARCHIVADO ${fechaArchivado}`
                                };

                            })
                            const data = expedientesBody.map(obj => headers.map(header => obj[header]));
                            const { jsPDF } = window.jspdf;
                            const doc = new jsPDF({
                                orientation: 'landscape',
                                // unit: 'in',
                                format: 'letter',
                            });

                            const img = new Image();
                            img.src = 'build/img/Sello_Corte_Suprema_de_Justicia_de_El_Salvador_(2021).png';

                            let inicio = false;
                            doc.autoTable({
                                willDrawPage: (data) => {
                                    // Crear un nuevo objeto de fecha para obtener la fecha y la hora actuales
                                    const currentDate = new Date();

                                    // Opciones de formato para obtener la fecha y la hora en formato de 12 horas
                                    const options = { year: 'numeric', month: 'numeric', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };

                                    // Formatear la fecha y la hora como una cadena legible
                                    const formattedDateTime = currentDate.toLocaleString('en-US', options);

                                    // Resto del código para el documento
                                    doc.setFont("helvetica");
                                    doc.setFont("helvetica", "normal", "bold");
                                    doc.setFontSize(14);
                                    doc.text("JUZGADO TERCERO DE VIGILANCIA PENITENCIARIA Y DE EJECUCIÓN DE LA PENA", 38, 13);
                                    doc.text("SAN MIGUEL", 120, 20);
                                    doc.text(`FECHA Y HORA: ${formattedDateTime}`, 38, 33);

                                    if (inicio == true) {
                                        return
                                    }
                                    const imagePath = 'build/img/logoPng.png';
                                    // Ajusta la posición y el tamaño de la imagen según tus necesidades
                                    doc.addImage(img, 'PNG', data.settings.margin.left, 5, 23, 23);
                                    doc.addImage(imagePath, 'PNG', 245, 5, 23, 23);
                                    inicio = true;

                                },

                                margin: { top: 35 },
                                headStyles: { fillColor: [49, 57, 69], fontSize: 12, },
                                bodyStyles: { fillColor: [255, 255, 255], textColor: [0, 0, 0], fontSize: 11, },
                                head: [headers],
                                body: data,
                                autoSize: true,

                            });

                            doc.save('expedientes_internos_asistidos.pdf');
                            ocultarMensajeCargando();

                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            }
        }, {
            className: 'select-button',
            text: 'Filtrar por estado:',
            action: function (e, dt, node, config) {
                // Este código se ejecutará al hacer clic en el botón desplegable
            },
            init: function (dt, node, config) {

                /* borrar lo que haya en el cuadro de busqueda search */

                const search =



                    // Agrega un select con la opción predeterminada "Seleccione una opción"
                    node.append('<select class="custom-select">' +
                        '<option value=""  selected>Todos</option>' +
                        '<option value="fd4d6e6b5623d265f5b3b3422aae62b28215d55fc6c67e2424f7d98b16df5b08">Archivados</option>' +
                        '<option value="c135f01ff8c8edf1b34a285e97a98b0e5019e6c5932ecb0d575e56f8a2f2b541">En Tramite</option>' +
                        '</select>');

                // Agrega un evento de cambio al select
                node.find('select').on('change', async function () {
                    estado = $(this).val();

                    /* Filtrar por esto con fetch */
                    table.ajax.url(`/expedientes?estado=${estado}`).load();
                });
            }
        }

        ]
        ,
        "fixedHeader": {
            // header: true,
            footer: true
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": `${location.origin}/expedientes`,
            "type": "GET"
        },
        "async": false,
        "error": function (xhr, error, thrown) {
            console.log("Error en la solicitud AJAX:", error);
        },
        "language": {
            url: 'build/language/es-ES.json',
        },
        "columns": [{
            "data": "id"
        },
        {
            "data": "tipo_expediente"
        },
        {
            "data": "n_expediente"
        },
        {
            "data": "nombre_usuario"
        },
        {
            "data": "delito"
        },
        {
            "data": "fecha_sentencia"
        },
        {
            "data": "pena"
        },
        {
            "data": "fecha_ingreso"
        },
        {
            "data": "observaciones"
        },
        {
            "data": "tipo_pena"
        },
        {
            orderable: false,
            className: 'action-buttons',
            render: function (data, type, row) {
                return '<div class="d-flex flex-column p-3 gap-2"><a class="btn btn-primary updateExpediente"  data-id="' + row.id + '">' +
                    '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>' +
                    '</a>'
                    +
                    '<a class="btn btn-danger deleteExpediente" data-id="' + row.id + '">' +
                    '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                    '</a>'
                    +
                    '<a style="cursor: pointer; font-size: 13px; white-space: nowrap;" class="btn fw-bold ' + (row.archivado == 0 ? 'btn-success' : 'btn-dark') + '  archivarExpediente" data-estado="' + row.archivado + '" data-id="' + row.id + '">' +
                    '' + (row.archivado == 0 ? "EN TRAMITE" : "ARCHIVADO")
                    + '</a>'
                    + (row.updated_at != null ? '<p class="text-muted mt-1 fw-bold" style="white-space: nowrap";">Fecha Archivado: ' + '<span class="fw-normal"> ' + row.updated_at + '</span>' + ' </p>' : '')

                    + '</div>';

            }
        }
        ],

    });

    function mostrarMensajeCargando() {
        // Crea un elemento div para el mensaje de carga
        const mensajeCargando = document.createElement('div');

        mensajeCargando.innerText = 'Generando PDF. Por favor, espere...';
        mensajeCargando.style.position = 'fixed';
        mensajeCargando.style.top = '50%';
        mensajeCargando.style.left = '50%';
        mensajeCargando.style.transform = 'translate(-50%, -50%)';
        mensajeCargando.style.backgroundColor = 'rgba(0, 0, 0, 1)';
        mensajeCargando.classList.add('text-center', 'p-3', 'rounded', 'shadow', 'text-white');
        mensajeCargando.style.padding = '20px';
        mensajeCargando.style.borderRadius = '10px';
        mensajeCargando.id = 'mensajeCargando';

        // Agrega el elemento al body
        const documentbody = document.querySelector('body');
        documentbody.appendChild(mensajeCargando);
    }

    function ocultarMensajeCargando() {
        // Busca el elemento por su id y lo elimina
        const mensajeCargando = document.getElementById('mensajeCargando');
        if (mensajeCargando) {
            document.body.removeChild(mensajeCargando);
        }
    }


    /* ARCHIVAR EXPEDIENTE */

    async function archivarExpediente(id, estadoactual) {

        try {
            const response = await fetch('/expedientes/archivar?id=' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    'estadoactual': estadoactual
                }),
            })
            const data = await response.json()
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: `Expediente ${data.success == "archivado" ? 'archivado' : 'desarchivado'} correctamente`,
                    showConfirmButton: true,

                })
                table.ajax.reload(null, false); // El segundo parámetro 'false' mantiene la página actual
            }
        } catch (error) {
            console.log(error)
        }

    }

    $(document).on('click', '.archivarExpediente', function () {
        const expedienteId = $(this).data('id');

        const expedienterow = table.row($(this).parents('tr'));

        $decision = expedienterow.data().archivado == 0 ? "Archivar" : "Desarchivar";

        // Hacer algo con el ID, como confirmar la eliminación
        Swal.fire({
            title: `¿Estás seguro de que deseas ${$decision} este Expediente?`,
            text: 'No podrás revertir esta acción',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Si, ${$decision}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (result.isConfirmed) {
                archivarExpediente(expedienteId, expedienterow.data().archivado)
            }

        })
    });


    /* EDITAR EXPEDIENTE */
    /* expedientes */
    const selectTipo_edit = document.getElementById('select-tipo-edit');

    const autocompleteInput_edit = document.getElementById('autocompleteInput-edit');
    const suggestionsList_edit = document.getElementById('suggestionsList-edit');

    const n_expediente_edit = document.getElementById('n-expediente-edit');
    const delito_edit = document.getElementById('input-delito-edit');
    const fecha_sentencia_edit = document.getElementById('fecha-sentencia-edit');
    const fecha_ingreso_edit = document.getElementById('fecha-ingreso-edit');
    const observaciones_edit = document.getElementById('observaciones-edit');
    const pena_accesoria_edit = document.getElementById('select-pena-accesoria-edit');
    const pena_edit = document.getElementById('input-pena-edit');


    const expedienteUpdate = {
        'id-interno': '',
        'select-tipo-edit': '',
        'n-expediente-edit': '',
        'input-delito-edit': '',
        'fecha-sentencia-edit': '',
        'input-pena-edit': '',
        'fecha-ingreso-edit': '',
        'select-pena-accesoria-edit': '',
        'observaciones-edit': '',
    }

    selectTipo_edit.addEventListener('change', (e) => {
        validacionedit(e)
    })

    n_expediente_edit.addEventListener('input', (e) => {
        validacionedit(e)
    })

    delito_edit.addEventListener('input', (e) => {
        validacionedit(e)
    })

    fecha_sentencia_edit.addEventListener('change', (e) => {
        validacionedit(e)
    })

    fecha_ingreso_edit.addEventListener('change', (e) => {
        validacionedit(e)
    })

    pena_accesoria_edit.addEventListener('change', (e) => {
        validacionedit(e)
    })

    pena_edit.addEventListener('input', (e) => {
        validacionedit(e)
    })

    observaciones_edit.addEventListener('input', (e) => {
        validacionedit(e)
    })

    let timeoutId_edit = null;
    let detener_edit = false;


    autocompleteInput_edit.addEventListener('input', async (e) => {
        const inputValue = e.target.value.trim();

        // Limpiar el temporizador anterior
        clearTimeout(timeoutId_edit);

        if (inputValue.length >= 2) {
            // Establecer un nuevo temporizador
            timeoutId_edit = setTimeout(async () => {
                try {
                    const response = await fetch('/users?search=' + inputValue);
                    const data = await response.json();

                    while (suggestionsList_edit.firstChild) {
                        suggestionsList_edit.removeChild(suggestionsList_edit.firstChild);
                    }

                    if (document.querySelector('#erroresidinterno')) {
                        document.querySelector('#erroresidinterno').remove();
                    }

                    if (data.length == 0) {
                        const p = document.createElement('p');
                        p.textContent = "No se encontraron resultados";
                        p.classList.add('text-danger', 'text-end', 'col-12', 'fw-semibold');
                        p.id = "erroresidinterno";
                        autocompleteInput_edit.parentElement.appendChild(p);
                    }
                    data.forEach(user => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('list-group-item', 'col-12', 'pe-auto');
                        listItem.innerHTML = user.id + ' - ' + user.name + ' - <strong>DUI: </strong>  ' + (user.dui.trim().length > 0 ? '<strong>' + user.dui + '</strong>' : ' <span class="text-danger fw-bold">SIN DUI</span> ');


                        listItem.addEventListener('click', () => {
                            while (suggestionsList_edit.firstChild) {
                                suggestionsList_edit.removeChild(suggestionsList_edit.firstChild);
                            }

                            detener = true;

                            if (document.querySelector('#userexistente_edit')) {
                                document.querySelector('#userexistente_edit').remove();
                            }

                            autocompleteInput_edit.value = user.name;
                            expedienteUpdate['id-interno'] = user.id;

                            if (document.querySelector('#erroresidinterno')) {
                                document.querySelector('#erroresidinterno').remove();
                            }

                            const p = document.createElement('p');
                            p.textContent = "Este usuario está registrado en el sistema";
                            p.classList.add('text-success', 'text-end', 'col-12', 'fw-semibold');
                            p.id = "userexistente_edit";
                            autocompleteInput_edit.parentElement.appendChild(p);
                        });

                        suggestionsList_edit.appendChild(listItem);
                    });
                } catch (error) {
                    console.log(error);
                }
            }, 300); // Establecer el temporizador a 500 milisegundos (ajusta según sea necesario)
        } else if (inputValue.length < 2) {
            suggestionsList_edit.innerHTML = '';
        }
    });

    autocompleteInput_edit.addEventListener('keyup', () => {
        detener_edit = false;
        expedienteUpdate['id-interno'] = '';

        if (document.querySelector('#userexistente_edit')) {
            document.querySelector('#userexistente_edit').remove();
        }
    });
    const validacionedit = (e) => {
        expedienteUpdate[e.target.id] = e.target.value
    }


    // let myModal = new bootstrap.Modal(document.getElementById('exampleModalToggle2'));
    const myModal = new bootstrap.Modal(document.getElementById('exampleModalToggle2'));

    const handleeditarexpedientepost = async (e) => {

        e.preventDefault()
        const id = document.getElementById('oculto-id-expediente').value
        erroresvalidacionUpdate()
        if (expedienteUpdate['id-interno'] == '' || expedienteUpdate['fecha-ingreso-edit'] == '' ||
            expedienteUpdate['fecha-sentencia-edit'] == '' || expedienteUpdate['select-tipo-edit'] == '' ||
            expedienteUpdate['input-delito-edit'].trim() == '' || expedienteUpdate['select-pena-accesoria-edit'] == '' ||
            expedienteUpdate['input-pena-edit'].trim() == '' || expedienteUpdate['n-expediente-edit'].trim() == '') {
            return
        }

        try {
            const response = await fetch(`/expedientes-update?id=${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(expedienteUpdate),
            });

            const data = await response.json();

            if (data.success == true) {

                Swal.fire({
                    "icon": "success",
                    "title": "Expediente actualizado correctamente",
                    "text": "Se actualizo correctamente el expediente",
                    "confirmButtonText": "OK"
                })
                // Actualiza la DataTable con
                table.ajax.reload(null, false); // El segundo parámetro 'false' mantiene la página actual
                // Cierra el modal
                myModal.hide();
                expedienteUpdate['id-interno'] = "";
                expedienteUpdate['select-tipo-edit'] = "";
                expedienteUpdate['n-expediente-edit'] = "";
                expedienteUpdate['input-delito-edit'] = "";
                expedienteUpdate['fecha-sentencia-edit'] = ""
                expedienteUpdate['input-pena-edit'] = "";
                expedienteUpdate['fecha-ingreso-edit'] = "";
                expedienteUpdate['select-pena-accesoria-edit'] = "";
                expedienteUpdate['observaciones-edit'] = "";

                if (document.querySelector('#userexistente_edit')) {
                    document.querySelector('#userexistente_edit').remove();
                }
            } else {
                data.errores.error.forEach((mensaje, index) => {
                    // Crear un nuevo Toast para cada mensaje de error
                    const toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 6000,
                        showCloseButton: true,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    toast.fire({
                        icon: "error",
                        title: mensaje
                    });
                });
                return
            }
        } catch (error) {
            console.log(error)
        }
    }

    const erroresvalidacionUpdate = () => {
        const errores = document.querySelectorAll('#erroresid')
        const erroridinterno = document.querySelector('#erroresidinterno')

        if (erroridinterno) {
            erroridinterno.remove()
        }

        if (errores) {
            errores.forEach(error => error.remove())
        }

        if (expedienteUpdate['id-interno'] == '') {
            validarError('Debe seleccionar el usuario, si no existe debe registrarlo previamente', '#autocompleteInput-edit', true)
        }
        if (expedienteUpdate['select-tipo-edit'] == '') {
            validarError('Debe seleccionar el tipo de expediente', '#select-tipo-edit')
        }
        if (expedienteUpdate['n-expediente-edit'].trim() == '') {
            validarError('Debe introducir el n° de expediente', '#n-expediente-edit')
        }

        if (expedienteUpdate['input-delito-edit'].trim() == '') {
            validarError('Debe introducir el delito', '#input-delito-edit')
        }

        if (expedienteUpdate['fecha-sentencia-edit'] == '') {
            validarError('Debe introducir la fecha de sentencia', '#fecha-sentencia-edit')
        }

        if (expedienteUpdate['input-pena-edit'].trim() == '') {
            validarError('Debe introducir la pena', '#input-pena-edit')
        }

        if (expedienteUpdate['fecha-ingreso-edit'] == '') {
            validarError('Debe introducir la fecha de ingreso', '#fecha-ingreso-edit')
        }
        if (expedienteUpdate['select-pena-accesoria-edit'] == '') {
            validarError('Debe seleccionar la pena accesoria', '#select-pena-accesoria-edit')
        }


    }

    const btnsaveedit = document.getElementById('btn-save-expedient-edit');
    btnsaveedit.addEventListener('click', handleeditarexpedientepost)


    $(document).on('click', '.updateExpediente', function () {

        const errores = document.querySelectorAll('#erroresid')
        const erroridinterno = document.querySelector('#erroresidinterno')

        if (document.querySelector('#userexistente_edit')) {
            document.querySelector('#userexistente_edit').remove();
        }

        if (erroridinterno) {
            erroridinterno.remove()
        }

        if (errores) {
            errores.forEach(error => error.remove())
        }

        const expedienteId = $(this).data('id');
        /* obtener objeto completo */
        const expedienterow = table.row($(this).parents('tr'));
        const dataobj = expedienterow.data()

        if (document.getElementById('oculto-id-expediente')) {
            document.getElementById('oculto-id-expediente').remove()
        }

        /* crear un input oculto con el id */
        const inputocultoidexpediente = document.createElement('input')
        inputocultoidexpediente.type = 'hidden'
        inputocultoidexpediente.value = expedienteId
        inputocultoidexpediente.id = 'oculto-id-expediente'

        /* insertarlo en a la en el padre del boton save */
        /* buscando el padre */
        const padrebtnsave = btnsaveedit.parentElement;
        padrebtnsave.appendChild(inputocultoidexpediente);

        expedienteUpdate['id-interno'] = dataobj.user_id
        expedienteUpdate['select-tipo-edit'] = dataobj.tipo_id
        expedienteUpdate['n-expediente-edit'] = dataobj.n_expediente
        expedienteUpdate['input-delito-edit'] = dataobj.delito
        expedienteUpdate['fecha-sentencia-edit'] = dataobj.fecha_sentencia
        expedienteUpdate['input-pena-edit'] = dataobj.pena
        expedienteUpdate['fecha-ingreso-edit'] = dataobj.fecha_ingreso
        expedienteUpdate['select-pena-accesoria-edit'] = dataobj.pena_id
        expedienteUpdate['observaciones-edit'] = dataobj.observaciones


        // Obtén el elemento select
        // Itera sobre las opciones y selecciona la que coincide con el tipoId
        for (var i = 0; i < selectTipo_edit.options.length; i++) {
            if (selectTipo_edit.options[i].value == dataobj.tipo_id) {
                selectTipo_edit.options[i].selected = true;
                break; // Rompe el bucle una vez que se haya encontrado y seleccionado el valor
            }
        }
        autocompleteInput_edit.value = dataobj.nombre_usuario
        n_expediente_edit.value = dataobj.n_expediente;
        delito_edit.value = dataobj.delito;
        fecha_sentencia_edit.value = dataobj.fecha_sentencia;
        fecha_ingreso_edit.value = dataobj.fecha_ingreso;
        observaciones_edit.value = dataobj.observaciones;
        pena_accesoria_edit.value = dataobj.pena_id
        pena_edit.value = dataobj.pena


        myModal.show();
    });

    /* ELIMINAR EXPEDIENTE */
    // Delegación de eventos para el botón de eliminación
    $(document).on('click', '.deleteExpediente', function () {
        const expedienteId = $(this).data('id');

        // Hacer algo con el ID, como confirmar la eliminación
        Swal.fire({
            title: '¿Estás seguro de que deseas eliminar este Expediente?',
            text: 'No podrás revertir esta acción',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                handleEliminarExpediente(expedienteId)
                Swal.fire(
                    'Eliminado!',
                    'El expediente ha sido eliminado.',
                    'success'
                )
            }
        })
    });
    const handleEliminarExpediente = async (id) => {
        try {
            const response = await fetch(`/expedientes-delete?id=${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Error en la solicitud DELETE');
            }

            const responseData = await response.json();

            if (responseData.success) {
                table.ajax.reload(null, false); // El segundo parámetro 'false' mantiene la página actual
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error al eliminar el expediente',
                })
            }
        } catch (error) {
            console.log(error)
        }
    }

    /* NUEVO EXPEDIENTE */
    /* Autocomplete */
    const autocompleteInput = document.getElementById('autocompleteInput')
    const suggestionsList = document.getElementById('suggestionsList')


    const selecttipo = document.getElementById('select-tipo')
    const nexpediente = document.getElementById('n-expediente')
    const inputdelito = document.getElementById('input-delito')
    const fechasentencia = document.getElementById('fecha-sentencia')
    const inputpena = document.getElementById('input-pena')
    const fechaingreso = document.getElementById('fecha-ingreso')
    const selectpena = document.getElementById('select-pena')
    const observaciones = document.getElementById('observaciones')

    const limpiarInputs = () => {

        autocompleteInput.value = '';
        suggestionsList.innerHTML = '';
        selecttipo.value = '';
        nexpediente.value = '';
        inputdelito.value = '';
        fechasentencia.value = '';
        inputpena.value = '';
        fechaingreso.value = '';
        selectpena.value = '';
        observaciones.value = '';
        autocompleteInput.value = '';
    }

    limpiarInputs()

    const expediente = {
        'id-interno': '',
        'select-tipo': '',
        'n-expediente': '',
        'input-delito': '',
        'fecha-sentencia': '',
        'input-pena': '',
        'fecha-ingreso': '',
        'select-pena': '',
        'observaciones': '',
    }


    /* NUEVO INTERNO  */

    function validarErrorUser(message, selector) {


        const error = document.createElement('p')
        error.id = 'errornewinterno'
        error.textContent = message
        error.classList.add('error', 'mb-0', 'mt-1', 'text-danger', 'fw-semibold')
        document.querySelector(selector).appendChild(error)
    }


    const nombreinterno = {
        "nombre": '',
        "dui": '',
    }

    /* nuevointerno */
    const nuevointernoInput = document.getElementById('newinterno-input')
    const nuevointernoDuiInput = document.getElementById('newinterno-dui-input')


    /* nuevo interno */
    nuevointernoInput.addEventListener('input', (e) => {
        nombreinterno['nombre'] = e.target.value
    })
    nuevointernoDuiInput.addEventListener('input', (e) => {
        nombreinterno['dui'] = e.target.value
    })

    const modal3 = new bootstrap.Modal(document.getElementById('exampleModalToggle3'));

    /* UPDATE INTERNO */
    function validarErrorUpdateUser(message, selector) {


        const error = document.createElement('p')
        error.id = 'errornewinterno-update'
        error.textContent = message
        error.classList.add('error', 'mb-0', 'mt-1', 'text-danger', 'fw-semibold')
        document.querySelector(selector).parentElement.appendChild(error)
    }

    const userupdateobj = {
        "id": '',
        'user-edit-input-name': '',
        'user-edit-input-dui': '',
    }

    /* llenar objeto datos nuevo */

    function llenarobjetouser(e) {

        userupdateobj[e.target.id] = e.target.value;

    }



    function validarErrorUpdateInterno() {

        if (userupdateobj['user-edit-input-name'].trim() == '') {

            validarErrorUpdateUser('Debe escribir el nombre del interno', '#user-edit-input-name')

        }
        // if (userupdateobj['user-edit-input-dui'].trim() == '') {

        //     validarErrorUpdateUser('Debe escribir el dui del interno', '#user-edit-input-dui')

        // }


        if (userupdateobj['user-edit-input-dui'].trim().length >= 1) {
            var patronDUI = /^\d{8}-\d{1}$/;

            // Verificar si el DUI coincide con el patrón
            if (!patronDUI.test(userupdateobj['user-edit-input-dui'])) {
                validarErrorUpdateUser('El formato del DUI es incorrecto, debe ser: 00000000-0', '#user-edit-input-dui')
            }

        }

    }


    async function handleeditarusuario(e) {
        e.preventDefault()
        const errors = document.querySelectorAll('#errornewinterno-update');

        if (errors) {
            errors.forEach(error => {
                error.remove();
            });
        }

        validarErrorUpdateInterno()

        var patronDUI = /^\d{8}-\d{1}$/;

        if (userupdateobj['user-edit-input-dui'].trim() != '') {
            if (!patronDUI.test(userupdateobj['user-edit-input-dui'])) {
                return
            }
        }

        if (userupdateobj['id'] == '' || userupdateobj['user-edit-input-name'].trim() == '') {
            return;
        }

        try {
            const response = await fetch('/users-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',

                },
                body: JSON.stringify({
                    "id": userupdateobj['id'],
                    "name": userupdateobj['user-edit-input-name'],
                    "dui": userupdateobj['user-edit-input-dui'],
                })
            })

            const data = await response.json()

            if (data.success == true) {
                Swal.fire({
                    "icon": "success",
                    "title": "Interno actualizado correctamente",
                    "text": "Se actualizo correctamente el interno",
                    "confirmButtonText": "OK"
                })

                modal3.hide();

                tableuser.ajax.reload(null, false);
                table.ajax.reload(null, false);
            } else {

                const dueñodui = data.dueñodui;


                const mensaje = `El dui numero ${dueñodui.dui} le pertenece a ${dueñodui.name}`;
                validarErrorUpdateUser(mensaje, '#user-edit-input-dui')
            }

        } catch (error) {
            console.log(error)
        }
    }

    const usernameupdate = document.getElementById('user-edit-input-name');
    const userduiupdate = document.getElementById('user-edit-input-dui');


    const btnupdateuser = document.getElementById('btn-update-user')

    btnupdateuser.addEventListener('click', handleeditarusuario)

    usernameupdate.addEventListener('input', llenarobjetouser)
    userduiupdate.addEventListener('input', llenarobjetouser)

    $(document).on('click', '.updateUser', function () {
        /* borrar mensajes de error previos */
        const errores = document.querySelectorAll('#errornewinterno-update');

        if (errores) {
            errores.forEach(error => {
                error.remove();
            });
        }
        /* obtener objeto completo */
        const userrow = tableuser.row($(this).parents('tr'));

        const objuser = userrow.data();

        userupdateobj['id'] = objuser['id']
        userupdateobj['user-edit-input-name'] = objuser['name']
        userupdateobj['user-edit-input-dui'] = objuser['dui']

        usernameupdate.value = objuser['name']
        userduiupdate.value = objuser['dui']


        // modal3.style.zIndex = 9999;
        // Mostrar modal3
        modal3.show();

    });



    function validarErrorNewInterno() {

        if (nombreinterno['nombre'].trim() == '') {
            validarErrorUser('Debe escribir el nombre del interno', '#newinterno-form')

        }
        // if (nombreinterno['dui'].trim() == '') {
        //     validarErrorUser('Debe escribir el dui del interno', '#newinterno-form')

        // }


        var patronDUI = /^\d{8}-\d{1}$/;
        if (nombreinterno['dui'].trim().length >= 1) {
            // Verificar si el DUI coincide con el patrón
            if (!patronDUI.test(nombreinterno['dui'])) {
                validarErrorUser('El formato del DUI es incorrecto, debe ser: 00000000-0', '#newinterno-form')
            }

        }


    }

    const handlesubmitNewInterno = async (e) => {
        e.preventDefault()

        const errores = document.querySelectorAll('#errornewinterno')
        if (errores) {
            errores.forEach(error => error.remove())
        }
        /* VALIDAR NO ESTE VACIO */
        validarErrorNewInterno()

        var patronDUI = /^\d{8}-\d{1}$/;
        if (nombreinterno['dui'].trim() != '') {
            // Verificar si el DUI coincide con el patrón
            if (!patronDUI.test(nombreinterno['dui'])) {
                return
            }
        }

        if (nombreinterno['nombre'].trim() == '') {
            return
        }

        /* convertir a maysculas y quitar tildes y acentos */
        nombreinterno['nombre'] = nombreinterno['nombre'].normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase()
        try {
            /* Crear un nuevo registro */
            const response = await fetch('/users-create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    'nombre': nombreinterno['nombre'],
                    'dui': nombreinterno['dui'].trim(),
                })
            })
            // Actualiza la DataTable con los datos actualizados


            const data = await response.json()
            if (data.success == true) {
                Swal.fire({
                    icon: 'success',
                    title: 'Interno creado correctamente',
                    showConfirmButton: false,
                    timer: 3500,
                    showConfirmButton: true,
                    showConfirmText: 'CERRAR',
                })

                tableuser.ajax.reload(null, false); // El segundo parámetro 'false' mantiene la página actual
            } else {

                data.errores.error.forEach((message, index) => {

                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 6000,
                        timerProgressBar: true,
                        showCloseButton: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "error",
                        title: message
                    });
                })
                return
            }
            if (document.querySelector('#newinterno-input')) {
                document.querySelector('#newinterno-input').value = ''
            }
            if (document.querySelector('#newinterno-dui-input')) {
                document.querySelector('#newinterno-dui-input').value = ''
            }

            /* vaciar objeto */
            nombreinterno['nombre'] = ''
            nombreinterno['dui'] = ''

        } catch (error) {
            console.log(error);
        }

    }


    document.getElementById('btn-save-newinterno').addEventListener('click',
        handlesubmitNewInterno
    )


    const validacion = (e) => {

        expediente[e.target.id] = e.target.value
    }


    /* Crear un nuevo registro */
    selecttipo.addEventListener('change', validacion)
    nexpediente.addEventListener('input', validacion)
    inputdelito.addEventListener('input', validacion)
    fechasentencia.addEventListener('change', validacion)
    inputpena.addEventListener('input', validacion)
    fechaingreso.addEventListener('change', validacion)
    selectpena.addEventListener('change', validacion)
    observaciones.addEventListener('input', validacion)

    let detener = false;
    let timeoutId = null;

    autocompleteInput.addEventListener('input', async (e) => {
        const inputValue = e.target.value.trim();

        // Limpiar el temporizador anterior
        clearTimeout(timeoutId);

        if (inputValue.length >= 2) {
            // Establecer un nuevo temporizador
            timeoutId = setTimeout(async () => {
                try {
                    const response = await fetch('/users?search=' + inputValue);
                    const data = await response.json();

                    while (suggestionsList.firstChild) {
                        suggestionsList.removeChild(suggestionsList.firstChild);
                    }

                    if (document.querySelector('#erroresidinterno')) {
                        document.querySelector('#erroresidinterno').remove();
                    }

                    if (data.length == 0) {
                        const p = document.createElement('p');
                        p.textContent = "No se encontraron resultados";
                        p.classList.add('text-danger', 'text-end', 'col-12', 'fw-semibold');
                        p.id = "erroresidinterno";
                        autocompleteInput.parentElement.appendChild(p);
                    }
                    data.forEach(user => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('list-group-item', 'col-12', 'pe-auto');

                        listItem.innerHTML = user.id + ' - ' + user.name + ' - <span class="fw-bold">DUI: </span>' + (user.dui.trim().length > 0 ? '<strong>' + user.dui + '</strong>' : '<span class="text-danger fw-bold">' + 'SIN DUI' + '</span>');


                        listItem.addEventListener('click', () => {
                            while (suggestionsList.firstChild) {
                                suggestionsList.removeChild(suggestionsList.firstChild);
                            }

                            detener = true;

                            if (document.querySelector('#userexistente')) {
                                document.querySelector('#userexistente').remove();
                            }

                            autocompleteInput.value = user.name;
                            expediente['id-interno'] = user.id;

                            if (document.querySelector('#erroresidinterno')) {
                                document.querySelector('#erroresidinterno').remove();
                            }

                            const p = document.createElement('p');
                            p.textContent = "Este usuario está registrado en el sistema";
                            p.classList.add('text-success', 'text-end', 'col-12', 'fw-semibold');
                            p.id = "userexistente";
                            autocompleteInput.parentElement.appendChild(p);
                        });

                        suggestionsList.appendChild(listItem);
                    });
                } catch (error) {
                    console.log(error);
                }
            }, 300); // Establecer el temporizador a 500 milisegundos (ajusta según sea necesario)
        } else if (inputValue.length < 2) {
            suggestionsList.innerHTML = '';
        }
    });

    autocompleteInput.addEventListener('keyup', () => {
        detener = false;
        expediente['id-interno'] = '';

        if (document.querySelector('#userexistente')) {
            document.querySelector('#userexistente').remove();
        }
    });

    /* Errores de validacion */
    const validarError = (error, ubicacion, modificar = false) => {
        const errorDiv = document.createElement('div');
        if (modificar) {
            errorDiv.id = "erroresidinterno";
        } else {
            errorDiv.id = "erroresid";
        }
        const cardBody = document.querySelector(ubicacion).parentElement;

        const parrafo = document.createElement('P');
        parrafo.textContent = error;
        parrafo.classList.add('text-danger', 'text-left', 'col-12', 'fw-semibold');
        errorDiv.appendChild(parrafo);
        cardBody.appendChild(errorDiv);
    }
    const erroresvalidacion = () => {
        const errores = document.querySelectorAll('#erroresid')
        const erroridinterno = document.querySelector('#erroresidinterno')

        if (erroridinterno) {
            erroridinterno.remove()
        }

        if (errores) {
            errores.forEach(error => error.remove())
        }

        if (expediente['id-interno'] == '') {
            validarError('Debe seleccionar el usuario, si no existe debe registrarlo previamente', '#autocompleteInput', true)
        }
        if (expediente['select-tipo'] == '') {
            validarError('Debe seleccionar el tipo de expediente', '#select-tipo')
        }
        if (expediente['n-expediente'].trim() == '') {
            validarError('Debe introducir el n° de expediente', '#n-expediente')
        }

        if (expediente['input-delito'].trim() == '') {
            validarError('Debe introducir el delito', '#input-delito')
        }

        if (expediente['fecha-sentencia'] == '') {
            validarError('Debe introducir la fecha de sentencia', '#fecha-sentencia')
        }

        if (expediente['input-pena'].trim() == '') {
            validarError('Debe introducir la pena', '#input-pena')
        }

        if (expediente['fecha-ingreso'] == '') {
            validarError('Debe introducir la fecha de ingreso', '#fecha-ingreso')
        }
        if (expediente['select-pena'] == '') {
            validarError('Debe seleccionar la pena accesoria', '#select-pena')
        }


    }

    const handlesubmit = async (e) => {
        e.preventDefault()
        erroresvalidacion()

        if (expediente['id-interno'] == '' || expediente['select-tipo'] == '' || expediente['n-expediente'].trim() == '' ||
            expediente['input-delito'].trim() == '' || expediente['fecha-sentencia'] == '' || expediente['input-pena'].trim() == '' ||
            expediente['fecha-ingreso'] == '' || expediente['select-pena'] == '') {
            return
        }
        try {
            /* Crear un nuevo registro */
            const response = await fetch(`/expedientes-create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(expediente)
            })
            // Actualiza la DataTable con los datos actualizados
            table.ajax.reload(null, false); // El segundo parámetro 'false' mantiene la página actual

            const data = await response.json()

            if (data.success == true) {
                Swal.fire({
                    icon: "success",
                    title: "Expediente creado correctamente"
                })
            } else {
                data.errores.error.forEach((mensaje, index) => {
                    // Crear un nuevo Toast para cada mensaje de error
                    const toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 6000,
                        showCloseButton: true,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    toast.fire({
                        icon: "error",
                        title: mensaje
                    });
                });

                return
            }

            limpiarInputs()

            /* vaciar el objeto expediente */

            expediente['id-interno'] = ''
            expediente['select-tipo'] = ''
            expediente['n-expediente'] = ''
            expediente['input-delito'] = ''
            expediente['fecha-sentencia'] = ''
            expediente['input-pena'] = ''
            expediente['fecha-ingreso'] = ''
            expediente['select-pena'] = ''
            expediente['observaciones'] = ''

            if (document.querySelector('#userexistente')) {
                document.querySelector('#userexistente').remove();
            }
        } catch (error) {
            console.log(error);
        }
    }

    document.getElementById('btn-save').addEventListener('click',
        handlesubmit
    )


    /* BARRA SCROLL */

    var scrollDown = document.getElementById('scrollDown');
    var scrollUp = document.getElementById('scrollUp');

    // Mostrar la flecha hacia abajo inicialmente
    scrollDown.style.display = 'block';

    // Mostrar u ocultar las flechas según el desplazamiento
    window.addEventListener('scroll', function () {
        if (window.scrollY > 100) {
            scrollUp.style.display = 'block';
            scrollDown.style.display = 'none';
        } else {
            scrollUp.style.display = 'none';
            scrollDown.style.display = 'block';
        }
    });

    // Flecha para desplazar hacia abajo
    scrollDown.addEventListener('click', function () {
        window.scrollTo({
            top: document.body.scrollHeight - window.innerHeight,
            behavior: 'smooth'
        });
    });

    // Flecha para desplazar hacia arriba
    scrollUp.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

})