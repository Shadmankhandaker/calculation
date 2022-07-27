<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Commission Fee</title>
 <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,900&display=swap" rel="stylesheet">

   <!-- <link rel="stylesheet" href="fonts/icomoon/style.css"> -->


    <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://www.astellhealthcare.com/websites/bootstrap.min.css"> 
    
    <!-- Style -->
    <link rel="stylesheet" href="https://www.astellhealthcare.com/websites/style.css">
   
        </head>
        <body>
            

    <div class="container">
      <div class="row align-items-stretch justify-content-center no-gutters">
        <div class="col-md-7">
          <div class="form h-100 contact-wrap p-5">
            <h3 class="text-center">Calculate Commision Fee</h3>
            <form class="mb-5" method="post" id="contactForm" enctype="multipart/form-data">
              @csrf

              <div class="row">
                <div class="col-md-12 form-group mb-3">
                  <label for="budget" class="col-form-label">Choose File</label>
                  <input type="file" class="form-control" name="get_file" id="subject" value="" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                </div>
              </div>

           
                
                
              
              <div class="row justify-content-center">
                <div class="col-md-5 form-group text-center">
                  <input type="submit" value="Calculate" class="btn btn-block btn-primary rounded-0 py-2 px-4" name="submit">
                  <span class="submitting"></span>
                </div>
              </div>
            </form>

            
           

          </div>
        </div>
      </div>
    </div>
        </body>
</html>