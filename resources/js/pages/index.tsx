import { Card } from '@/components/ui/card'
import { Button, Input } from '@headlessui/react'
import { Head, useForm, usePage } from '@inertiajs/react'
import { useState } from 'react'
import { useRoute } from 'ziggy-js'

type Stats = {
    sum_of_sold_houses: number
    crimes_by_year: Record<number, number>
    average_price: {
        overall: number
        by_year_and_area: Record<number, Record<string, number>>
    }
}
type Duplicates = null | { date: string; area: string }[]
export default function Index() {
    const { stats } = usePage<{ stats: Stats | null }>().props
    const route = useRoute()
    const { data, setData, post, processing, wasSuccessful } = useForm<{
        housing_document: null | File
    }>({
        housing_document: null,
    })
    const [duplicates, setDuplicates] = useState<Duplicates>(null)
    const [errors, setErrors] = useState<[string, string][]>([])

    const handleSubmit = () => {
        if (processing) return

        setDuplicates(null)
        setErrors([])

        post(route('housing.document.upload'), {
            onSuccess: (res) => {
                console.log('success', res)
            },
            onError: (err) => {
                if (err?.duplicates) {
                    setDuplicates(JSON.parse(err.duplicates))
                    return
                }
                setErrors(Object.entries(err))
                console.log('error!', err)
            },
        })
    }

    console.log('stats', stats)
    console.log('duplicates', duplicates)
    const isSubmittingDisabled = processing || data.housing_document === null

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
                        <Button
                            className={`bg-blue-${isSubmittingDisabled ? '300' : '700'} p-1`}
                            onClick={handleSubmit}
                            disabled={isSubmittingDisabled}
                        >
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
                {!processing && errors.length > 0 && (
                    <div className='mt-3 inline-block bg-red-600 p-1 text-white'>
                        <strong>Error!</strong>
                        <ul>
                            {Object.entries(errors).map(([key, value]) => (
                                <li key={key}>{value}</li>
                            ))}
                        </ul>
                    </div>
                )}
                {!processing && duplicates && (
                    <div className='mt-3 inline-block bg-orange-600 p-1 text-white'>
                        <strong>Warning</strong>
                        <p>
                            There were duplicated records in the uploaded document. All records were
                            successfully stored in the database except the following ones:
                        </p>
                        <ul>
                            {duplicates.map(({ date, area }) => (
                                <li key={`${date}-${area}`}>
                                    {date} - {area}
                                </li>
                            ))}
                        </ul>
                        <p>Review this records and you can later upload only the fixed ones.</p>
                    </div>
                )}

                <Card className='mt-6 bg-stone-300 pl-3 text-black'>
                    {stats === null ? (
                        <div>There is no data to calculate. Upload a document first.</div>
                    ) : (
                        <>
                            <div>
                                <strong>Avg price:</strong>{' '}
                                {stats.average_price.overall.toLocaleString('en-US')}
                            </div>
                            <div>
                                <strong>Total houses sold:</strong> {stats.sum_of_sold_houses}
                            </div>
                            <div>
                                <strong>No of crimes in 2011:</strong>{' '}
                                {stats.crimes_by_year?.[2011] ?? 0}
                            </div>
                            <div>
                                <strong>Avg price by year in London area</strong>
                                {Object.entries(stats.average_price.by_year_and_area)
                                    .filter(([, data]) => !!data?.['city of london'])
                                    .map(([year, data]) => (
                                        <div key={year}>
                                            {year}: {data['city of london'].toLocaleString('en-US')}
                                        </div>
                                    ))}
                            </div>
                        </>
                    )}
                </Card>
            </div>
        </>
    )
}
