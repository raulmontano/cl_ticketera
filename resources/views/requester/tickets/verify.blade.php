<script>
(function () {

  fetch('https://somosclave.cl/login/session-check.php', {
    credentials: 'include'
  })
  .then(function (res) {

    if (!res.ok) {
      window.location.href = '{{route('requester.access-denied',['not_logged'=>true]) }}';
      return null;
    }

    return res.json();
  })
  .then(function (data) {
    
    if (!data || !data.token) {    
      window.location.href = '{{route('requester.access-denied',['not_logged'=>true]) }}';
      return;
    }

    // Crear formulario POST
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'https://gc.somosclave.cl/public/requester/tickets/create';
    
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'somosclave_token';
    input.value = data.token;

    form.appendChild(input);
    document.body.appendChild(form);

    form.submit();
  });

})();
</script>

