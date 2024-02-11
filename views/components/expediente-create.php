<?php require_once 'user-update.php' ?>

<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalToggleLabel">NUEVO EXPEDIENTE</h5>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">


                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">REGISTRAR EXPEDIENTE</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">REGISTRAR INTERNO/ASISTIDO</button>
                    </li>

                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="card">
                            <div class="card-header fw-bold text-center  fs-5">REGISTRAR EXPEDIENTE</div>
                            <div class="card-body p-3">
                                <form method="POST">
                                    <div class="row g-3 align-items-center">
                                        <div class=" col-md-6">

                                            <div class="mb-3">
                                                <div class="d-flex">
                                                    <label for="recipient-name" class="form-label fw-semibold flex-wrap">BUSCAR
                                                        INTERNO/ASISTIDO<span class="text-danger fw-semibold fs-5">*</span></label>

                                                </div>

                                                <div class="form-group has-search">
                                                    <span class="fa fa-search form-control-feedback"></span>
                                                    <input required autocomplete="off" id="autocompleteInput" type="input" class="form-control">
                                                    <ul style="position: absolute;
                                                    width: inherit; /* Hace que el ancho sea igual al del input */
                                                    margin: 0;
                                                    padding: 0;
                                                    border: 1px solid #ccc;
                                                    z-index: 1000;
                                                    overflow-y: auto; /* Agrega una barra de desplazamiento vertical si es necesario */
                                                    max-height: 200px; /* Establece la altura máxima y agrega una barra de desplazamiento si la lista es muy larga */
                                                    " class="list-group" id="suggestionsList">
                                                </div>


                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">TIPO<span class="text-danger fw-semibold fs-5">*</span></label>
                                                <select class="form-select " id="select-tipo">
                                                    <option value="">Seleccione un Tipo</option>
                                                    <?php foreach ($tipos as $tipo) : ?>
                                                        <option value="<?php echo $tipo->id ?>"><?php echo $tipo->nombre ?></option>
                                                        <?php // Puedes agregar más contenido aquí según tus necesidades 
                                                        ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">N° EXPEDIENTE<span class="text-danger fw-semibold fs-5">*</span></label>
                                                <input required id="n-expediente" type="text" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">DELITO<span class="text-danger fw-semibold fs-5">*</span></label>
                                                <input required id="input-delito" type="text" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">FECHA DE SENTENCIA<span class="text-danger fw-semibold fs-5">*</span></label>
                                                <input required id="fecha-sentencia" type="date" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">

                                            <div class="mb-3">
                                                <label class="form-label">PENA/PLAZO<span class="text-danger fw-semibold fs-5">*</span></label>
                                                <input required id="input-pena" type="text" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">FECHA DE INGRESO<span class="text-danger fw-semibold fs-5">*</span></label>
                                                <input required id="fecha-ingreso" type="date" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"> PENAS ACCESORIAS<span class="text-danger fw-semibold fs-5">*</span></label>
                                                <select class="form-select" id="select-pena">
                                                    <option value="">Seleccione una Pena</option>
                                                    <?php foreach ($penas as $pena) : ?>
                                                        <option value="<?php echo $pena->id ?>"><?php echo $pena->nombre ?></option>

                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">OBSERVACIONES</label>
                                                <textarea id="observaciones" class="form-control" style="height: 124px;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="btn-save" type="submit" class="btn btn-primary">Guardar</button>
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

                        <div class="card mt-3">

                            <div class="card-header fw-bold text-center  fs-5">LISTADO DE INTERNOS/ASISTIDOS</div>
                            <div class="card-body">
                                <form id="newinterno-form" method="POST">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group mb-3 mb-md-0">
                                                    <label for="newinterno-input" class="form-label fw-semibold">REGISTRAR NUEVO INTERNO/ASISTIDO</label>
                                                    <input required autocomplete="off" id="newinterno-input" type="text" placeholder="EJEMPLO: RAUL ANTONIO SERPAS FUNES" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label for="newinterno-dui-input" class="form-label fw-semibold">DUI</label>
                                                    <input required autocomplete="off" id="newinterno-dui-input" type="text" placeholder="EJEMPLO: 05053426-3" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 d-md-flex align-items-md-end justify-content-md-end">
                                                <button type="submit" id="btn-save-newinterno" class="btn btn-primary mt-2 mt-md-0">Guardar</button>
                                            </div>
                                        </div>
                                    </div>


                                </form>


                                <div class="table-responsive mt-5">
                                    <table id="users-table" class="display" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>DUI</th>
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
            </div>
        </div>
    </div>
</div>