<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="admin_home.php">Admin Dashboard</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item ">
        <a class="nav-link" href="">Logged in as <?php echo $_SESSION["username"]; ?></a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="manage_users.php">Manage Users</a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="add_admin.php">Add Admin</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="add_courses.php">Manages Courses</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="add_videos.php">add videos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="add_quizes.php">add quizes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="add_coupon.php">Add Coupon</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="comments.php">User Comments</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>
