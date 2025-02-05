<div class="mb-3">
    <label for="nombre" class="form-label">Nombre</label>
    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo isset($nombre) ? ($nombre) : '' ?>" required>
</div>
<div class="mb-3">
    <label for="apellidos" class="form-label">Apellidos</label>
    <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo isset($apellidos) ? ($apellidos) : '' ?>" required>
</div>

<div class="mb-3">
    <label for="rol" class="form-label">Rol</label>
    <select class="form-control" id="rol" name="rol">
    <option value="0" <?php echo ($rol == 0) ? 'selected' : ''; ?>>Usuario Registrado</option>
    <option value="1" <?php echo ($rol == 1) ? 'selected' : ''; ?>>Administrador</option>
     </select>
</div>

<div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? ($username) : '' ?>" required>
</div>