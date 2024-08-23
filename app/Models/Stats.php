<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Stats extends Model
{
    use HasFactory;

    public function calculateAndUpdateTimeFocused($id)
    {
        $user = User::find($id);

        if ($user) { // Check if the user exists
            //$user->
            $totalTimeFocused = $user->projects()->sum('time_focused_total');
            $user->update(['time_focused' => $totalTimeFocused]);
        }
    }

}
