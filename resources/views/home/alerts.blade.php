
@if (Auth::user()->notifications->isEmpty())
<p class="text-muted">No tienes alertas.</p>
@else
@foreach (Auth::user()->notifications as $notification)

<ul class="list-group">
    <li class="list-group-item list-group-item-danger d-flex justify-content-between align-items-center">
    {{ $notification->data['menssage'] }}
      <form action="{{ route('notifications.delete', $notification->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="summit" class="btn-close" aria-label="Close"></button>
        </form>
    </li>
  </ul>
@endforeach
@endif