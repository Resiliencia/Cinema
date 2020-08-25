<?php
    require_once 'autenticacion_admin.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
            require_once 'secciones/head.php';
        ?>
    </head>
    <body>
<?php
    require_once 'secciones/navbar.php';
    
    require_once 'clases/conexion.php';
    require_once 'clases/Funcion.php';
    require_once 'clases/Pelicula.php';
    $control=false;
    $conexion = Conexion::establecer();
    if($conexion->connect_error){
        echo '<div class="container">';
        echo '<div class="alert alert-danger" style="padding: 10px;">';
        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        echo '<strong>ERROR: '.$conexion->connect_error.'</strong>';
        echo '</div>';
        echo '</div>';
    }
    else{
        $accion = filter_input(INPUT_POST,"accion",FILTER_SANITIZE_STRING);
        $funcion = new Funcion();
        $id = filter_input(INPUT_GET,"id",FILTER_VALIDATE_INT);
        if(!$id) $id = filter_input(INPUT_POST,"id",FILTER_VALIDATE_INT);
        if(!$id){ 
            echo '<div class="container">';
            echo '<div class="alert alert-danger" style="padding: 10px;">';
            echo '<strong>ERROR: no se encontró el ID de la función.</strong>';
            echo '</div>';
            echo '</div>';
            echo '<div class="link-to-home"><a href="index.php" class="btn btn-link">Volver a la página principal</a></div>';
            die();
        }
        else{
            if(!$funcion->cargar($conexion, $id)){
                echo '<div class="container">';
                echo '<div class="alert alert-danger" style="padding: 10px;">';
                echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                echo '<strong>ERROR: '.$funcion->getError().'</strong>';
                echo '</div>';
                echo '</div>';
            }
            else{
                $pelicula = new Pelicula();
                $pelicula->cargar($conexion, $funcion->getPelicula());
                $fechaFuncion = DateTime::createFromFormat('Y-m-d H:i:s', $funcion->getFecha());
                
                if($accion=="eliminar"){
                    if($funcion->eliminar($conexion)){
                        $control=true;
                    }
                    else{
                        echo '<div class="container">';
                        echo '<div class="alert alert-danger" style="padding: 10px;">';
                        echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
                        echo '<strong>ERROR: '.$funcion->getError().'</strong>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
        }
        $conexion->close();
    }
?>
<?php 
    if(!$control){
?>
        <div class="row">
            <div id="ventanaConfirm" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Confirmación</h4>
                        </div>
                        <div class="modal-body">
                            <p>¿Eliminar la función de la base de datos?</p>
                        </div>
                        <div class="modal-footer">
                            <form method="post" action="files/funcion_eliminar.php" class="form-horizontal">
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                <input type="hidden" name="accion" value="eliminar"/>
                                <input type="hidden" name="id" value="<?php echo $id;?>"/>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-1 col-sm-3 col-md-4 col-lg-4"></div>
            <div class="page-header text-center col-xs-10 col-sm-6 col-md-4 col-lg-4">
                <h2>Eliminar función</h2>
            </div>
            <div class="col-xs-1 col-sm-3 col-md-4 col-lg-4"></div>
        </div>
        <div class="row">
            <div class="col-xs-1 col-sm-3 col-md-4 col-lg-3"></div>
            <div class="container-fluid col-xs-10 col-sm-6 col-md-4 col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title">Función seleccionada</h2>
                    </div>
                    <div class="panel-body table-responsive text-center">
                        <table class="table table-hover table-bordered">
                            <thead class="alert-info">
                                <tr>
                                    <th>ID</th> 
                                    <th>Película</th>                    
                                    <th>Sala</th>
                                    <th>Fecha</th>
                                    <th>Horario</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
                                echo '<td>'.$funcion->getId().'</td>';
                                echo '<td>'.$pelicula->getNombre().'</td>';
                                echo '<td>'.$funcion->getSala().'</td>';
                                echo '<td>'.$fechaFuncion->format('d/m/Y').'</td>';
                                echo '<td>'.$fechaFuncion->format('H:i').'</td>';
?>
                            </tbody>
                        </table>    
                    </div>
                </div>
            </div>
            <div class="col-xs-1 col-sm-3 col-md-4 col-lg-3"></div>
        </div>
        <div class="row">
            <div class="col-xs-1 col-sm-3 col-md-4 col-lg-5"></div>
            <div class="container-fluid col-xs-10 col-sm-6 col-md-4 col-lg-2">
                <form method="post" action="files/funcion_eliminar.php" class="form">
                    <div class="form-group">
                        <button id="eliminar" name="eliminar" type="button" onclick="confirmacion()" class="btn btn-warning form-control">Eliminar</button>
                    </div>
                </form>
                <form method="post" action="files/funcion_busqueda.php" class="form">
                    <div class="form-group">
                        <button id="cancelar" name="cancelar" type="submit" class="btn btn-default form-control">Cancelar</button>
                    </div>
                </form>
            </div>
            <div class="col-xs-1 col-sm-3 col-md-4 col-lg-5"></div>
        </div>
<?php 
    }
    else{
?>
        <div class="row">
            <div class="col-xs-1 col-sm-4 col-md-3 col-lg-3"></div>
            <div class="container text-center col-xs-10 col-sm-4 col-md-6 col-lg-6">
                <div class="page-header text-center">
                    <h2>Eliminar función</h2>
                </div>
                <div class="alert alert-success" style="padding: 10px;">
                    <strong>La función ha sido eliminada de la base de datos.</strong>
                </div>
            </div>
            <div class="col-xs-1 col-sm-4 col-md-3 col-lg-3"></div>
        </div>
        <div class="row row-centrada">
            <a id="btn-regresar" href="files/funcion_busqueda.php" class="link-mid"><img src="img/botones/regresar-negro.svg" class="link-img"></a>
        </div>
<?php
    }
?>
    </body>
</html>