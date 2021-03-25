<html>
<head>
    {% set title = "Tianpeng's Project" %}
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<h1>{{ title }}</h1>

<div>
    {% block content %}
    {% endblock %}
</div>

</body>
</html>