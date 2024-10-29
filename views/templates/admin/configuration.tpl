<h2>Gestion des Listes</h2>

<!-- Bouton pour ouvrir le modal -->
<button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#createListModal">
    Créer une nouvelle liste
</button>

<!-- Table des listes -->
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Actif</th>
            <th>Actions</th>

        </tr>
    </thead>
    <tbody>
        {foreach from=$lists item=list}
            <tr>
                <td>{$list.id_s2i_list}</td>
                <td>{$list.name|escape}</td>
                <td>
                    {if $list.active}
                        <span class="badge badge-success">Oui</span>
                    {else}
                        <span class="badge badge-danger">Non</span>
                    {/if}
                </td>


                <td>
                    <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                        <div class="btn-group dropend" role="group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Action
                            </button>
                            <ul class="dropdown-menu">
                                <li> <a class="dropdown-item" href="{$modify_link}{$list.id_s2i_list}">Modifier</a>
                                </li>
                                <li> <a class="dropdown-item" href="{$delete_link}{$list.id_s2i_list}"
                                        onclick="return confirm('Supprimer cette liste ?');">Supprimer</a> </li>
                            </ul>
                        </div>
                    </div>


                </td>
            </tr>
        {/foreach}
    </tbody>
</table>

<!-- Modal Bootstrap pour le formulaire de création de liste -->
<div class="modal fade" id="createListModal" tabindex="-1" role="dialog" aria-labelledby="createListModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createListModalLabel">Nouvelle Liste</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{$create_link}" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <!-- Switch moderne pour Actif -->
                    <div class="form-group">
                        <label for="active" class="switch-label">Actif</label>
                        <label class="switch">
                            <input type="checkbox" id="active" name="active" value="1" checked>
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <!-- Switch moderne pour Slider -->
                    <div class="form-group">
                        <label for="slider" class="switch-label">Slider</label>
                        <label class="switch">
                            <input type="checkbox" id="slider" name="slider" value="1">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="speed">Vitesse du Slider (en ms)</label>
                        <input type="number" class="form-control" id="speed" name="speed" value="5000">
                    </div>

                    <div class="form-group">
                        <label for="image">Image principale</label>
                        <input type="file" class="form-control-file" id="image" name="image">
                    </div>

                    <!-- Switch moderne pour Image mobile -->
                    <div class="form-group">
                        <label for="is_mobile_image" class="switch-label">Image pour mobile ?</label>
                        <label class="switch">
                            <input type="checkbox" id="mobile_image_checkbox" onclick="updateMobileImageField()">
                            <span class="slider round"></span>
                        </label>
                        <small class="form-text text-muted">Cochez pour une version mobile.</small>
                        <!-- Champ caché qui permet de switch entre 1 et 0 pour notifier la version mobile-->
                        <input type="hidden" id="is_mobile_image" name="is_mobile_image" value="0">
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer la liste</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function updateMobileImageField() {
        document.getElementById('is_mobile_image').value = document.getElementById('mobile_image_checkbox').checked ?
            1 : 0;
    }
</script>