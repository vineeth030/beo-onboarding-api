<div>
    @if($isClient)
        <p>Hi,</p>

        <p>
            We would like to inform you that the offer letter for
            a candidate has been successfully generated.
        </p>

        <p>
            All relevant details regarding the offer — including compensation, joining date,
            and terms of employment — have been included in the attached document.
        </p>

        <p>
            Kindly review the attachment at your convenience.<br>
            If you need any clarification or further changes, please feel free to reach out.
        </p>
    @else
        <p>Hi,</p>

        {!! $content !!}
    @endif
</div>