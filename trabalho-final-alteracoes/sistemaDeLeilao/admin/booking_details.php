<?php
include('includes/checklogin.php');
check_login();
if(isset($_REQUEST['eid']))
{
  $eid=intval($_GET['eid']);
  $status="2";
  $sql = "UPDATE tblbooking SET Status=:status WHERE  id=:eid";
  $query = $dbh->prepare($sql);
  $query -> bindParam(':status',$status, PDO::PARAM_STR);
  $query-> bindParam(':eid',$eid, PDO::PARAM_STR);
  $query -> execute();
  echo "<script>alert('Booking Successfully Cancelled');</script>";
  echo "<script type='text/javascript'> document.location = 'cancelled_bookings.php; </script>";
}


if(isset($_REQUEST['aeid']))
{
  $aeid=intval($_GET['aeid']);
  $status=1;

  $sql = "UPDATE tblbooking SET Status=:status WHERE  id=:aeid";
  $query = $dbh->prepare($sql);
  $query -> bindParam(':status',$status, PDO::PARAM_STR);
  $query-> bindParam(':aeid',$aeid, PDO::PARAM_STR);
  $query -> execute();
  echo "<script>alert('Booking Successfully Confirmed');</script>";
  echo "<script type='text/javascript'> document.location = 'confirmed_bookings.php'; </script>";
}


?>
<!DOCTYPE html>
<html lang="pt">
<?php @include("includes/head.php");?>
<body>
  <div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    <?php @include("includes/header.php");?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:../../partials/_sidebar.html -->
      <?php @include("includes/sidebar.php");?>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="modal-header">
                  <h5 class="modal-title" style="float: left;">Detalhes da oferta</h5>
                </div>
                <div class="table-responsive p-3" id="print">
                  <table class="table align-items-center table-flush table-hover table-bordered" id="">
                   <tbody>
                    <?php 
                    $bid=intval($_GET['bid']);
                    $sql = "SELECT tblusers.*,tblbrands.BrandName,tblvehicles.VehiclesTitle,tblbooking.FromDate,tblbooking.ToDate,tblbooking.message,tblbooking.VehicleId as vid,tblbooking.Status,tblbooking.PostingDate,tblbooking.id,tblbooking.BookingNumber,
                    DATEDIFF(tblbooking.ToDate,tblbooking.FromDate) as totalnodays,tblvehicles.PricePerDay
                    from tblbooking join tblvehicles on tblvehicles.id=tblbooking.VehicleId join tblusers on tblusers.EmailId=tblbooking.userEmail join tblbrands on tblvehicles.VehiclesBrand=tblbrands.id where tblbooking.id=:bid";
                    $query = $dbh -> prepare($sql);
                    $query -> bindParam(':bid',$bid, PDO::PARAM_STR);
                    $query->execute();
                    $results=$query->fetchAll(PDO::FETCH_OBJ);
                    $cnt=1;
                    if($query->rowCount() > 0)
                    {
                      foreach($results as $result)
                      {     
                        ?>  
                        <h3 style="text-align:center; color:red">#<?php echo htmlentities($result->BookingNumber);?> Detalhes da oferta </h3>

                        <tr>
                          <th colspan="4" style="text-align:center;color:blue">Detalhes do usuario</th>
                        </tr>
                        <tr>
                          <th>Numero da oferta.</th>
                          <td>#<?php echo htmlentities($result->BookingNumber);?></td>
                          <th>Nome</th>
                          <td><?php echo htmlentities($result->FullName);?></td>
                        </tr>
                        <tr>                      
                          <th>Email</th>
                          <td><?php echo htmlentities($result->EmailId);?></td>
                          <th>Contacto</th>
                          <td><?php echo htmlentities($result->ContactNo);?></td>
                        </tr>
                        <tr>                      
                          <th>Ender??o</th>
                          <td><?php echo htmlentities($result->Address);?></td>
                          <th>Cidade</th>
                          <td><?php echo htmlentities($result->City);?></td>
                        </tr>
                        <tr>                      
                          <th>Provincia</th>
                          <td colspan="3"><?php echo htmlentities($result->Country);?></td>
                        </tr>

                        <tr>
                          <th colspan="4" style="text-align:center;color:blue">Detalhes da oferta</th>
                        </tr>
                        <tr>                      
                          <th>Nome do carro</th>
                          <td><a href="edit_car.php?id=<?php echo htmlentities($result->vid);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></td>
                            <th>Data da oferta</th>
                            <td><?php echo htmlentities($result->PostingDate);?>
                          </td>
                        </tr>
                        <tr>
                          <th>Da data</th>
                          <td><?php echo htmlentities($result->FromDate);?></td>
                          <th>At?? a data</th>
                          <td><?php echo htmlentities($result->ToDate);?></td>
                        </tr>
                        <tr>
                          <th>Total de dias</th>
                          <td><?php echo htmlentities($tdays=$result->totalnodays);?></td>
                          <th>Valor da oferta</th>
                          <td><?php echo htmlentities($ppdays=$result->PricePerDay);?></td>
                        </tr>
                        <tr>
                          <th colspan="3" style="text-align:center">total</th>
                          <td><?php echo htmlentities($tdays*$ppdays);?></td>
                        </tr>
                        <tr>
                          <th>Estado da oferta</th>
                          <td><?php 
                          if($result->Status==0)
                          {
                            echo htmlentities('N??o confirmado ainda');
                          } else if ($result->Status==1) {
                            echo htmlentities('Confirmado');
                          }
                          else{
                            echo htmlentities('Cancelado');
                          }
                          ?></td>
                          <th>Data da ??ltima atualiza????o</th>
                          <td><?php echo htmlentities($result->LastUpdationDate);?></td>
                        </tr>

                        <?php 
                        if($result->Status==0)
                        { 
                          ?>
                          <tr>  
                            <td style="text-align:center" colspan="4">
                              <a href="booking_details.php?aeid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Voc?? realmente deseja Confirmar esta oferta')" class="btn btn-primary"> Confirmar Oferta</a> 

                              <a href="booking_details.php?eid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Deseja realmente cancelar esta oferta')" class="btn btn-danger"> Cancelar Oferta</a>
                            </td>
                          </tr>
                          <?php 
                        } ?>
                        <?php $cnt=$cnt+1; 
                      }
                    } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
      <!-- partial:../../partials/_footer.html -->
      <?php @include("includes/footer.php");?>
      <!-- partial -->
    </div>
    <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<?php @include("includes/foot.php");?>
<!-- End custom js for this page -->

</body>
</html>