<br>
<br>
<br>
<footer class="footer-bs">
    <div class="row">
        <div class="col-md-3 footer-brand animated fadeInLeft">
            <div>
                <img src="{{ asset("img/uvaeth_black_sf.png") }}" alt="" width="80%" height="45%">
            </div>
            <br>
            <b>Universidad Virtual AETH</b>
            <p>En AETH estamos comprometidos con tu desarrollo</p>
        </div>
        <div class="col-md-4 footer-nav animated fadeInUp">
            <div class="col-md-6">
                <ul class="list">
                    <li><a href="#">Acerca de</a></li>
                    <li><a href="#">Contacto</a></li>
                    <li><a href="#">Términos y condiciones</a></li>
                    <li><a href="#">Pólitica de privacidad</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-2 footer-social animated fadeInDown">
            <h4>Síguenos</h4>
            <ul>
                <li><a href="#">Facebook</a></li>
                <li><a href="#">Twitter</a></li>
                <li><a href="#">Instagram</a></li>
            </ul>
        </div>
        <div class="col-md-3 footer-ns animated fadeInRight">
            <h4>¿Preguntas?</h4>
            <p>Envia tu pregunta y te contestaremos a la brevedad</p>
            <form action="{{ route('notify.question') }}" method="post">
                @csrf
                <p>
                    <div class="input-group">
                        <textarea onclick="onComments()" id="commentsAreaId" style="font-size: 80%;" class="form-control input-sm" name="question" rows="4" cols="50" required>Si tienes alguna duda, sugerencia o comentario, escríbenos.</textarea>
                    </div>
                </p>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-12 footer-brand">
            <p>Copyright © Software Aplicado. Todos los derechos reservados {{ \Carbon\Carbon::now()->year }}.</p>
        </div>
    </div>
    <script>
        function onComments() {
            if ($('#commentsAreaId').val() == "Si tienes alguna duda, sugerencia o comentario, escríbenos.") {
                $('#commentsAreaId').text('');
            }
        }
    </script>
</footer>