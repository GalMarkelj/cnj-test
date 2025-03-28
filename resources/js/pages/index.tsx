import { type SharedData } from '@/types'
import { Button, Input } from '@headlessui/react'
import { Head, useForm, usePage } from '@inertiajs/react'
import { useRoute } from 'ziggy-js'

export default function Index() {
    const { housings, stats } = usePage<SharedData>().props
    const route = useRoute()
    const { setData, post, errors, processing, wasSuccessful, hasErrors } = useForm<{
        housing_document: undefined | File
    }>({
        housing_document: null,
    })

    const handleSubmit = () => {
        if (processing) return
        post(route('housing.document.upload'))
    }

    console.log('housings', housings)
    console.log('stats', stats)

    return (
        <>
            <Head title='Index'>
                <link rel='preconnect' href='https://fonts.bunny.net' />
                <link
                    href='https://fonts.bunny.net/css?family=instrument-sans:400,500,600'
                    rel='stylesheet'
                />
            </Head>
            <div className='p-6'>
                <form>
                    <Input
                        type='file'
                        accept='.csv'
                        onChange={(e) => {
                            if (e.target.files === null) return
                            setData({ housing_document: e.target.files[0] })
                        }}
                    />
                    <div className='mt-3'>
                        <Button className='bg-blue-700 p-1' onClick={handleSubmit}>
                            Submit
                        </Button>
                    </div>
                </form>
                {processing && <div className='mt-3 text-yellow-300'>Loading...</div>}
                {!processing && wasSuccessful && (
                    <div className='mt-3 inline-block bg-green-600 p-1 text-white'>
                        <strong>Success!</strong>
                        <p>Data successfully saved into the database.</p>
                    </div>
                )}
                {!processing && hasErrors && (
                    <div className='mt-3 inline-block bg-red-600 p-1 text-white'>
                        <strong>Error!</strong>
                        <ul>
                            {Object.entries(errors).map(([key, value]) => (
                                <li key={key}>{value}</li>
                            ))}
                        </ul>
                    </div>
                )}
            </div>
        </>
    )
}
