import { type SharedData } from '@/types'
import { Button, Input } from '@headlessui/react'
import { Head, useForm, usePage } from '@inertiajs/react'
import { useRoute } from 'ziggy-js'

export default function Index() {
    const { auth } = usePage<SharedData>().props
    const route = useRoute()
    const { data, setData, post, errors, processing } = useForm<{ housing_document: null | File }>({
        housing_document: null,
    })

    console.log('data', data)

    const handleSubmit = () => {
        if (processing) return
        post(route('housing.document.upload'))
    }

    return (
        <>
            <Head title='Index'>
                <link rel='preconnect' href='https://fonts.bunny.net' />
                <link
                    href='https://fonts.bunny.net/css?family=instrument-sans:400,500,600'
                    rel='stylesheet'
                />
            </Head>
            <div className='flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 lg:justify-center lg:p-8 dark:bg-stone-700'>
                <form>
                    <Input
                        type='file'
                        accept='.csv'
                        onChange={(e) => {
                            if (e.target.files === null) return
                            setData({ housing_document: e.target.files[0] })
                        }}
                    />
                    <Button onClick={handleSubmit}>Submit</Button>
                </form>
            </div>
        </>
    )
}
