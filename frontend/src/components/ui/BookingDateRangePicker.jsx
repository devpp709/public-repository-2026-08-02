import React from 'react';
import { DateRangePicker } from 'rsuite';
import 'rsuite/dist/rsuite.min.css';

export default function BookingDateRangePicker({
                                                   value,
                                                   onChange,
                                                   placeholder
                                               }) {

    return (
        <DateRangePicker
            value={value}
            onChange={onChange}
            format="MM/dd/yyyy HH:mm"
            placeholder={placeholder}
            showOneCalendar={false}
            ranges={[]}
            editable={false}
            placement="bottomStart"
            cleanable
        />
    );
}