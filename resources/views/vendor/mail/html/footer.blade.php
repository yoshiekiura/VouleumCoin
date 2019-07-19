<tr>
    <td>
        <table class="footer" align="center" width="620" cellpadding="0" cellspacing="0">
            <tr>
                <td class="content-cell" align="center">
                    {{ Illuminate\Mail\Markdown::parse($slot) }}
                    <ul class="social">
                    @php
                    $links = json_decode( get_setting('site_social_links') );
                    @endphp
                    @if(isset($links->facebook))
                        <li><a href="{{$links->facebook}}"><img src="{{asset('assets/images/icons/brand-fb.png')}}" alt="FaceBook"></a></li>
                    @endif
                    @if(isset($links->twitter))
                        <li><a href="{{$links->twitter}}"><img src="{{asset('assets/images/icons/brand-tw.png')}}" alt="Twitter"></a></li>
                    @endif
                    @if(isset($links->linkedin))
                        <li><a href="{{$links->linkedin}}"><img src="{{asset('assets/images/icons/brand-in.png')}}" alt="LinkedIn"></a></li>
                    @endif
                    @if(isset($links->github))
                        <li><a href="{{$links->github}}"><img src="{{asset('assets/images/icons/brand-git.png')}}" alt="Github"></a></li>
                    @endif
                    </ul>
                </td>
            </tr>
        </table>
    </td>
</tr>
