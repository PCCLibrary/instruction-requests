<li class="nav-item">
    <a href="{{ route('requests.index') }}"
       class="nav-link {{ Request::is('requests*') ? 'active' : '' }}">
        <p>Requests</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('instructors.index') }}"
       class="nav-link {{ Request::is('instructors*') ? 'active' : '' }}">
        <p>Instructors</p>
    </a>
</li>


