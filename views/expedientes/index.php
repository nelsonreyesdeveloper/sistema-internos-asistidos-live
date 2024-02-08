<?php require_once __DIR__ . '/../components/expediente-create.php'; ?>
<?php require_once __DIR__ . '/../components/expediente-update.php'; ?>

<div style="width: 90%; margin:0 auto;">
    <div class="row px-0 my-4">
        <div class="col-md-6">

            <div class="col-md-10 d-md-flex justify-content-md-start">
                <div>
                    <a style="display: block;" class="btn btn-warning mb-2 mb-md-0 text-black fw-bold w-100" target="_blank" href="/backup">GENERAR COPIA DE SEGURIDAD</a>

                </div>
            </div>

        </div>

        <div class="col-md-6 d-md-flex justify-content-md-end">
            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-danger text-white fw-bold w-100">Cerrar Sesión</button>
            </form>
        </div>
    </div>




    <div class="card">

        <div class=" card-header fw-bold text-center fs-5">EXPEDIENTES INTERNOS/ASISTIDOS
        </div>
        <div class="card-body">


            <div class="p-3 pt-0">
                <div class="d-flex justify-content-between">
                    <a class="btn btn-primary my-2 mb-4" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Nuevo
                        Expediente
                    </a>

                </div>


                <div class="table-responsive">
                    <table id="expedientes-table" class="display" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>TIPO</th>
                                <th>N° EXPEDIENTE</th>
                                <th>NOMBRE INTERNO/ASISTIDO</th>
                                <th>DELITO</th>
                                <th>FECHA SENTENCIA</th>
                                <th>PENA</th>
                                <th>FECHA INGRESO</th>
                                <th>OBSERVACIONES</th>
                                <th>PENA ACCESORIA</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>


                        <tbody>


                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>
<!-- Vincular DataTables con Bootstrap 5 -->
<script src="build/js/jquery-3.7.0.js"></script>
<script src="build/js/jquery.dataTables.min.js"></script>

<script src="build/js/dataTables.buttons.min.js"></script>
<script src="build/js/jszip.min.js"></script>
<script src="build/js/pdfmake.min.js"></script>
<script src="build/js/vfs_fonts.js"></script>
<script src="build/js/buttons.html5.min.js"></script>
<script src="build/js/sweetalert2.all.js"></script>


<script src="build/js/app.js"></script>