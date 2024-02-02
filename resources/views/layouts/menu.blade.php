<li class="nav-item">
    <a href="{{ route('instructionRequests.index') }}"
       class="nav-link {{ Request::is('instructionRequests*') ? 'active' : '' }}">
        <i class="fa fa-file"></i> <span>Instruction Requests</span>
    </a>
</li>

<li class="dropdown-divider"></li>

<li class="nav-item">
    <a href="{{ route('instructors.index') }}"
       class="nav-link {{ Request::is('instructors*') ? 'active' : '' }}">
        <i class="fa fa-graduation-cap"></i> <span>Instructors</span>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('campuses.index') }}"
       class="nav-link {{ Request::is('campuses*') ? 'active' : '' }}">
        <i class="fa fa-globe"></i> <span>Campuses</span>
    </a>
</li>


<li class="nav-item">
    <a href="{!! route('users.index') !!}"
    class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
        <i class="fa fa-user"></i> <span>Librarians</span></a>
</li>



