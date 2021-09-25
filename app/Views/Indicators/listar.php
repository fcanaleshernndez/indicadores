<?= $header ?>

<h1 class="text-center">¡Visualiza los Indicadores! </h1><br>

<div class="container">

  <div class="row">

    <div class="col-md-12">
      <ul class="nav nav-tabs justify-content-center" id="ulPadre">
        <li class="nav-item">
          <a class="nav-link" href="#" id="botonUF">UF</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" id="botonDolar">Dolar</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" id="botonBitcoin">Bitcoin</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" id="botonEuro">Euro</a>
        </li>
      </ul>

    </div>

  </div><br>

  <div class="row justify-content-center">
    <div class="col-md-9">
      <canvas class="chart"></canvas>
    </div>
  </div><br>


  <div class="row justify-content-center">

    <button class="btn btn-outline-dark" id="addLastUF">Agregar últimos registros de UF</button>

  </div><br><br>

  <div class="row">

    <table class="table text-center">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Fecha</th>
          <th scope="col">Valor</th>
          <th scope="col">Acciones</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach ($indicadores->getResult() as $key => $value) { ?>

          <tr>
            <td><?= ++$key ?></td>
            <td><?= date("d-m-Y", strtotime($value->fecha)) ?></td>
            <td>$<?= number_format(round($value->valor), 0, ",", ".") ?></td>
            <td>
              <button class="btn btn-warning" onclick="getData(<?= $value->id ?>)" data-bs-toggle="modal" data-bs-target="#modalEditar"><i class="fas fa-edit fa-lg" style="color: white;"></i></button>
              <button class="btn btn-danger" onclick="eliminarFila(<?= $value->id ?>)"><i class="fas fa-trash-alt fa-lg"></i></button>
            </td>
          </tr>

        <?php } ?>

      </tbody>
    </table>
  </div>

</div>
<br><br><br><br><br>

<!-- MODALES -->
<div class="container">


  <!-- Editar Fila -->
  <div class="modal fade" id="modalEditar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloModal">Editar Registro de UF</h5>
        </div>
        <div class="modal-body">

          <form>
            <div class="row mb-1">
              <div class="col">
                <label class="form-label">Fecha: </label>
                <input type="date" id="fecha" class="form-control" required>
              </div>
              <div class="col">
                <label class="form-label">Valor: </label>
                <input type="number" id="valor" class="form-control" step="1000" required>
              </div>
            </div>

          </form>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-primary" id="btnEditar">Editar Registro</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Editar Fila -->
  <div class="modal fade" id="modalEnunciado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Indicaciones de la postulación</h5>
        </div>
        <div class="modal-body">

          <div class="card">
            <div class="card-body" style="text-align: justify;">
              Usando los datos de la <a href="https://www.mindicador.cl" style="text-decoration: none;" target="_blank"><strong> API (mindicador.cl)</strong></a>, 
              generar un gráfico en donde pueda seleccionar tipo de indicador y despliegue datos con fecha desde - hasta. <br><br>
              Hacer un mantenedor de los datos históricos de UF, y permitir modificarlos a través de un CRUD <strong>(Requisito PHP Codeigniter, usando AJAX)</strong>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>


</div>

<?= $footer ?>

<script>
  /* Modulos de Tippy.JS */
  tippy('#addLastUF', {
    content: 'Presiona este botón para agregar a la Base de datos los últimos 30 días de valor de la UF.',
  });

  tippy('#botonUF', {
    content: 'Se desplegará el valor de la UF.',
  });

  tippy('#botonDolar', {
    content: 'Se desplegará el valor del Dolar.',
  });

  tippy('#botonBitcoin', {
    content: 'Se desplegará el valor del Bitcoin.',
  });

  tippy('#botonEuro', {
    content: 'Se desplegará el valor del Euro.',
  });

  tippy('#idLinkedin', {
    content: 'Ver Linkedin',
  });

  tippy('#idWhatsapp', {
    content: 'Enviar un WhatsApp directo',
  });

  tippy('#idGmail', {
    content: 'Contáctar por correo',
  });

  let chartUF;

  const dibujarGrafico = (idBotonLi, indicador, idChart) => {

    document.querySelector(`#${idBotonLi}`).setAttribute('class', 'nav-link active');

    $.post('<?= base_url() ?>/IndicadoresController/mindicadorUF', {
      indicador: `${indicador}`
    }, (data) => {
      //console.log(data);

      const objDatosUF = JSON.parse(data);

      //console.log(objDatosUF);

      let fechas = objDatosUF.map((f) => f.fecha);

      let valores = objDatosUF.map((v) => v.valor);

      document.querySelector(`.chart`).setAttribute('id', `chart${idChart}`);

      let ctx = document.getElementById(`chart${idChart}`).getContext('2d');


      const config = {
        type: 'line',
        data: {
          labels: fechas.sort(),
          datasets: [{
            label: `${indicador.toUpperCase()}`,
            backgroundColor: 'rgb(255, 255, 255, 0.8)',
            borderColor: 'rgb(0, 173, 203)',
            data: valores.sort(),
          }]
        }
      }

      chartUF = new Chart(ctx, config);

    });

  }

  document.addEventListener("DOMContentLoaded", function(event) {

    const $botonUF = document.querySelectorAll('#ulPadre li')[0];
    const $botonDolar = document.querySelectorAll('#ulPadre li')[1];
    const $botonBTC = document.querySelectorAll('#ulPadre li')[2];
    const $botonEuro = document.querySelectorAll('#ulPadre li')[3];

    $botonUF.addEventListener('click', (e) => {

      document.querySelector('#botonBitcoin').setAttribute('class', 'nav-link');
      document.querySelector('#botonDolar').setAttribute('class', 'nav-link');
      document.querySelector('#botonEuro').setAttribute('class', 'nav-link');

      if (chartUF) {

        chartUF.destroy();

      } else {
        console.log('no existe la instancia');
      }

      const idBoton = "botonUF";
      const indicador = "uf";
      let idChart = 'uf';

      dibujarGrafico(idBoton, indicador, idChart);

    });

    $botonDolar.addEventListener('click', (e) => {

      document.querySelector('#botonBitcoin').setAttribute('class', 'nav-link');
      document.querySelector('#botonUF').setAttribute('class', 'nav-link');
      document.querySelector('#botonEuro').setAttribute('class', 'nav-link');

      if (chartUF) {

        chartUF.destroy();

      } else {

        console.log('no existe la instancia');

      }

      const idBoton = "botonDolar";
      const indicador = "dolar";
      let idChart = 'dolar';

      dibujarGrafico(idBoton, indicador, idChart);

    });

    $botonBTC.addEventListener('click', (e) => {

      document.querySelector('#botonDolar').setAttribute('class', 'nav-link');
      document.querySelector('#botonUF').setAttribute('class', 'nav-link');
      document.querySelector('#botonEuro').setAttribute('class', 'nav-link');

      if (chartUF) {

        chartUF.destroy();

      } else {

        console.log('no existe la instancia');

      }

      const idBoton = "botonBitcoin";
      const indicador = "bitcoin";
      let idChart = 'btc';

      dibujarGrafico(idBoton, indicador, idChart);

    });

    $botonEuro.addEventListener('click', (e) => {

      document.querySelector('#botonDolar').setAttribute('class', 'nav-link');
      document.querySelector('#botonUF').setAttribute('class', 'nav-link');
      document.querySelector('#botonBitcoin').setAttribute('class', 'nav-link');

      if (chartUF) {

        chartUF.destroy();

      } else {

        console.log('no existe la instancia');

      }

      const idBoton = "botonEuro";
      const indicador = "euro";
      let idChart = 'euro';

      dibujarGrafico(idBoton, indicador, idChart);

    });


    const $btnLastUF = document.querySelector('#addLastUF').addEventListener('click', () => {

      Swal.fire({
        icon: 'question',
        title: 'Agregar Múltiples Registros',
        text: 'Esta acción agregará a la base de datos el valor histórico de UF de los últimos 30 días (No borrará los anteriores ingresados).',
        showCancelButton: true,
        confirmButtonText: 'Si, agregar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {

          $.post('<?= base_url() ?>/IndicadoresController/agregarUltimoMesUF', {
            accion: "addLastMonth"
          }, (data) => {

            console.log(data);

            if (data == "exito") {

              Swal.fire(
                'Perfecto!',
                'Se insertaron a la base de datos, todos los registros del ultimo mes de UF',
                'success'
              ).then((result) => {

                location.reload();

              })

            } else {

              Swal.fire(
                'Error',
                'Hubo un error mientras se realizaba la acción, inténtalo nuevamente.',
                'error'
              )


            }
          })

        }
      })

    });
  })

  const eliminarFila = (id) => {

    Swal.fire({
      icon: 'question',
      title: 'Eliminar',
      text: '¿Seguro que deseas eliminar este registro?',
      showCancelButton: true,
      confirmButtonText: 'Si, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {

        $.post('<?= base_url() ?>/IndicadoresController/eliminarFila', {
          id: id
        }, (data) => {

          console.log(data);

          if (data !== "error") {
            Swal.fire(
              'Perfecto!',
              `Se elimino correctamente el registro`,
              'success'
            ).then((result) => {
              location.reload();
            })
          } else {
            Swal.fire(
              'Error!',
              `Hubo un error mientras se realizaba la acción, inténtalo nuevamente.`,
              'error'
            )
          }


        })

      }

    })

  }

  const getData = (id) => {

    $.post('<?= base_url() ?>/IndicadoresController/getFila', {
      id: id
    }, (data) => {

      //console.log(data);
      const objFila = JSON.parse(data);
      //console.log(objFila);

      document.querySelector('#fecha').value = objFila[0].fecha;
      document.querySelector('#valor').value = objFila[0].valor;

      document.querySelector('#btnEditar').setAttribute('onclick', `editarFila(${id})`);

    });
  }


  const editarFila = (id) => {

    const $fecha = document.querySelector('#fecha').value;
    const $valor = document.querySelector('#valor').value;

    console.log(`${$fecha} - ${$valor}`);



    Swal.fire({
      icon: 'question',
      title: 'Editar',
      text: '¿Seguro que deseas editar este registro?',
      showCancelButton: true,
      confirmButtonText: 'Si, editar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {

      if (result.isConfirmed) {

        $.post('<?= base_url() ?>/IndicadoresController/updateFila', {
          id: id,
          fecha: $fecha,
          valor: $valor
        }, (data) => {

          if (data == "exito") {

            Swal.fire(
              'Perfecto!',
              `Se editó correctamente el registro`,
              'success'
            ).then((result) => {
              location.reload();
            })

          } else {

            Swal.fire(
              'Error!',
              `Hubo un error mientras se realizaba la acción, inténtalo nuevamente.`,
              'error'
            )

          }

        })

      }

    })

  }
</script>