{% extends 'index.volt' %}

{% block content %}
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            {% for header in tableHeaders %}
                <th>{{ header }}</th>
            {% endfor %}
        </tr>
        </thead>
        <tbody>
        {% for record in tableData %}
            <tr>
                {% for key in tableHeaders|keys %}

                    <td>{{ record[key] }}</td>
                {% endfor %}
            </tr>
        {% endfor %}
        </tbody>
    </table>
    <ul>
        <li>
            Total pages crawled: {{ tableData|length }}
        </li>
        {% for key in summaryFieldNames|keys %}
        <li>
            {{ summaryFieldNames[key] }}: {{ summary[key] }}
        </li>
        {% endfor %}
    </ul>
{% endblock %}