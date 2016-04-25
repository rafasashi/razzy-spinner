<!Doctype html>
<html charset='en_US'>
<head>
<title>Razzy Article Spinner</title>
<link rel='stylesheet' media='all' href='assets/css/materialize.min.css' />
<link rel='stylesheet' media='all' href='assets/css/inputTags.min.css' />
<link rel='stylesheet' media='all' href='assets/css/sweetalert2.min.css' />

<script src='https://code.jquery.com/jquery-1.12.3.min.js' type='text/javascript'></script>
<script src='assets/js/materialize.min.js' type='text/javascript'></script>
<script src='assets/js/inputTags.jquery.min.js' type='text/javascript'></script>
<script src='assets/js/sweetalert2.min.js' type='text/javascript'></script>
<script src='assets/js/jquery.blockUI.js' type='text/javascript'></script>
<script src='assets/js/app.js' type='text/javascript'></script>
<meta name="description" content="Spin or Rewrite Articles " />
<style>
html,body{
  width:100% !important;
  height:100%  !important;
}

.main_container{
  background : url(assets/img/bg3.jpg) no-repeat center center fixed;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
  margin : 0 !important;
padding: 0 !important;
height:100%;
}

textarea{
  font-size : 20px !important;
  padding:5px;
}

.logo{
  margin : 0 auto !important;
  max-width:100% !important;
}

#spinned_article_area{
width:100% !important;border:none;padding:10px; !important;height:400px;min-height:200px;color : #000 !important;
}
</style>
</head>
<body>
  <div class="main_container">
<div align='center'>
<img class='logo' src='assets/img/logo.png' alt='Razzy Spinner Demo' />
</div>
  <div class="row">
  <div class="col s12 l8 m7">
<form id="article_spinner_form">
<div class='card-panel'>
  <div class="input-field">
<textarea name='article'  class="materialize-textarea" id="article_area"></textarea>
 <label for="article_area">Enter Article</label>
 </div>
</div>
</div>

<div class="col s12 l4 m5">
<div class='card-panel'>
  <p>
  <label for="ignore_words">Words to ignore</label>
<input type='text' name='ignore_words' value=1 class='browser-default' id='ignore_words'>
</p>
<p>
     <input type="checkbox" value=1 name="ignore_quoted_words" id="ignore_quoted_words" />
      <label for="ignore_quoted_words">Ignore quoted words</label>
    </p>

    <p>
         <input type="checkbox" value=1  name="ignore_capitalised_words" id="ignore_capitalised_words" />
          <label for="ignore_capitalised_words">Ignore capitalised words</label>
        </p>

        <p>
             <input type="checkbox" value=1 name="ignore_braced_words" id="ignore_braced_words" />
              <label for="ignore_braced_words">Ignore words braces eg: (word)</label>
        </p>
</div>
</div>

<div class='col s12 l12 m12'>
  <button type="submit" name="run_spin" class="btn btn-large wave-effect waves-light" style="width:100%">Rewrite Article</button>
</div>
</div>
</form>

<div class="row" id='spinned_article_row' style="display:none;">
    <div class="col s12 l12 m12">
    <div class='card-panel'>
    <div class="input-field">
     <textarea class="materialize-textarea active" readonly="readonly"  id="spinned_article_area" ></textarea>
      <label for="spinned_article_area">Spinned Article</label>
    </div>
  </div>
    </div>
  </div>
</div>

</div>
</body>
</html>
