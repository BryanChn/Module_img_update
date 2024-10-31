<h2>Gestion des Sections</h2>


<a href="#" id="show-create-section-form" class="btn btn-primary mb-3">+ </a>


<div id="create-section-form" style="display: none; margin-top: 20px;">

    {$section_form}
</div>




<div>
    {$sectionsList}

</div>

<script>
    document.getElementById('show-create-section-form').addEventListener('click', function(event) {
        event.preventDefault();
        var form = document.getElementById('create-section-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });
</script>