<!DOCTYPE html>
<html>
<head>
    <title>Teste de Upload</title>
</head>
<body>
    <h1>Teste de Upload</h1>
    <form action="/upload-teste" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
