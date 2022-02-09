function markOnLeave(id, submit) {
  let on_leave = document.getElementById(id);
  on_leave.addEventListener('change', ()=>{
    document.getElementById(submit).click();
  });
}
var image_crop = $('.image-crop').croppie({
  enableExif: true,
  viewport: {
      width: 200,
      height: 200,
      type: 'square'
  },
  boundary: {
      width: 300,
      height: 300,
  }
});

$('.crop-wrapper').hide();
$('.profile-image').on('change', function(){
  $('.crop-wrapper').show();
  $('.profile-wrapper').hide();
  var reader = new FileReader();
  reader.onload = function(e){
  image_crop.croppie('bind', {
      url: e.target.result
  }).then(function(){
      console.log('jQuery bind complete');
      console.log(reader.readAsDataURL(this.files[0]));
  });
  }
  reader.readAsDataURL(this.files[0]);
});

$('#change').on('click', function(ev){
  image_crop.croppie('result', {
      type: 'base64',
      size: 'viewport',
  }).then(function(blob){
      var val = document.getElementById('images');
      var fil = val.value = blob.slice(22);
      console.log(fil);
  });
});