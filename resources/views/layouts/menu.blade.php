
<li class="nav-item">
    <a href="{{ route('instructors.index') }}"
       class="nav-link {{ Request::is('instructors*') ? 'active' : '' }}">
        <p>Instructors</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('campuses.index') }}"
       class="nav-link {{ Request::is('campuses*') ? 'active' : '' }}">
        <p>Campuses</p>
    </a>
</li>



<li class="{{ Request::is('users*') ? 'active' : '' }}">
    <a href="{!! route('users.index') !!}"><i class="fa fa-user"></i><span>Users</span></a>
</li>
