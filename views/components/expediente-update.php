<div class="modal fade" id="exampleModalToggle2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">EDITAR EXPEDIENTE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header fw-bold text-center  fs-5">EDITAR EXPEDIENTE</div>
                    <div class="card-body p-3">
                        <form method="POST">
                            <div class="row g-3 align-items-center">
                                <div class=" col-md-6">

                                    <div class="mb-3">
                                        <div class="d-flex">
                                            <label for="recipient-name" class="form-label fw-semibold flex-wrap">BUSCAR
                                                INTERNO<span class="text-danger fw-semibold fs-5">*</span></label>

                                        </div>

                                        <div class="form-group has-search">
                                            <span class="fa fa-search form-control-feedback"></span>
                                            <input required autocomplete="off" id="autocompleteInput-edit" type="input" class="form-control">
                                            <ul style="position: absolute;
                                                    width: inherit; /* Hace que el ancho sea igual al del input */
                                                    margin: 0;
                                                    padding: 0;
                                                    border: 1px solid #ccc;
                                                    z-index: 1000;
                                                    overflow-y: auto; /* Agrega una barra de desplazamiento vertical si es necesario */
                                                    max-height: 200px; /* Establece la altura máxima y agrega una barra de desplazamiento si la lista es muy larga */
                                                    " class="list-group" id="suggestionsList-edit">
                                        </div>


                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">TIPO<span class="text-danger fw-semibold fs-5">*</span></label>
                                        <select class="form-select " id="select-tipo-edit">
                                            <option value="">Seleccione un Tipo</option>
                                            <?php foreach ($tipos as $tipo) : ?>
                                                <option value="<?= $tipo->id ?>"><?= $tipo->nombre ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">N° EXPEDIENTE<span class="text-danger fw-semibold fs-5">*</span></label>
                                        <input required id="n-expediente-edit" type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">DELITO<span class="text-danger fw-semibold fs-5">*</span></label>
                                        <input required id="input-delito-edit" type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">FECHA DE SENTENCIA<span class="text-danger fw-semibold fs-5">*</span></label>
                                        <input required id="fecha-sentencia-edit" type="date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label class="form-label">PENA/PLAZO<span class="text-danger fw-semibold fs-5">*</span></label>
                                        <input required id="input-pena-edit" type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">FECHA DE INGRESO<span class="text-danger fw-semibold fs-5">*</span></label>
                                        <input required id="fecha-ingreso-edit" type="date" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"> PENAS ACCESORIAS<span class="text-danger fw-semibold fs-5">*</span></label>
                                        <select class="form-select" id="select-pena-accesoria-edit">
                                            <option value="">Seleccione una Pena</option>
                                            <?php foreach ($penas as $pena) : ?>
                                                <option value="<?= $pena->id ?>"><?= $pena->nombre ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">OBSERVACIONES</label>
                                        <textarea id="observaciones-edit" class="form-control" style="height: 124px;"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button id="btn-save-expedient-edit" type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>