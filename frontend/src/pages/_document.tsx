// src/pages/_document.tsx
import { Html, Head, Main, NextScript } from 'next/document';
import React, { ReactElement } from 'react';

export default function Document(): ReactElement {
    return (
        <Html>
            <Head>
                <link
                    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
                    rel="stylesheet"
                />
                <link
                    rel="stylesheet"
                    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
                />
            </Head>
            <body>
            <Main />
            <NextScript />
            </body>
        </Html>
    );
}