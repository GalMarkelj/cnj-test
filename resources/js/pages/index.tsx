import { Card } from '@/components/ui/card'
import { Checkbox } from '@/components/ui/checkbox'
import { Button, Input } from '@headlessui/react'
import { Head, useForm, usePage } from '@inertiajs/react'
import { FormEvent, useMemo, useState } from 'react'
import { useRoute } from 'ziggy-js'

type Stats = {
    sum_of_sold_houses: number
    crimes_by_year: Record<number, number>
    average_price: {
        overall: number
        by_year_and_area: Record<number, Record<string, number>>
    }
}
type Duplicates = { date: string; area: string }[]
export default () => {
    const { stats } = usePage<{ stats: Stats | null }>().props
    const route = useRoute()
    const { setData, post, processing, wasSuccessful, hasErrors } = useForm<{
        housing_document: null | File
    }>({
        housing_document: null,
    })

    const [duplicates, setDuplicates] = useState<Duplicates>([])
    const [error, setError] = useState<null | string>(null)

    const londonYearlyPrices = useMemo(() => {
        if (!stats) return []
        return Object.entries(stats.average_price.by_year_and_area)
            .filter(([, data]) => !!data?.['city of london'])
            .map(([year, data]) => ({
                year,
                price: data['city of london'].toLocaleString('en-US'),
            }))
    }, [stats])

    const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        if (processing) return

        setDuplicates([])
        setError(null)

        post(route('housing.document.upload'), {
            onError: (err) => {
                console.log('error', err)
                setDuplicates(JSON.parse(err.duplicates))
                setError(err.message)
            },
        })
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
            <div className='p-6'>
                <Card className='mt-6 bg-stone-300 pl-3 text-black'>
                    <form onSubmit={handleSubmit}>
                        <Input
                            type='file'
                            accept='.csv'
                            onChange={(e) => {
                                if (e.target.files === null) return
                                setData({ housing_document: e.target.files[0] })
                            }}
                            required
                        />

                        <div className='mt-3 flex items-center space-x-2'>
                            <Checkbox id='confirmation' required />
                            <label
                                htmlFor='confirmation'
                                className='text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70'
                            >
                                Save to database
                            </label>
                        </div>

                        <div className='mt-3'>
                            <Button
                                className={`bg-blue-${processing ? '300' : '700'} p-1 text-white`}
                                disabled={processing}
                                type='submit'
                            >
                                Submit
                            </Button>
                        </div>
                    </form>
                </Card>

                {processing && <div className='mt-6 text-yellow-300'>Loading...</div>}
                {!processing && wasSuccessful && (
                    <div className='mt-6 inline-block bg-green-600 p-1 text-white'>
                        <strong>Success!</strong>
                        <p>Data successfully saved into the database.</p>
                    </div>
                )}
                {!processing && hasErrors && (
                    <div className='mt-6 inline-block bg-red-600 p-1 text-white'>
                        <strong>
                            Error! The data store was not successful due to the following errors:
                        </strong>
                        <div>{error}</div>
                        {duplicates.length > 0 && (
                            <div className='mt-2'>
                                <strong>Duplicated records:</strong>
                                <ul>
                                    {duplicates.map(({ date, area }) => (
                                        <li key={`${date}-${area}`}>
                                            {date} - {area}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        )}
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
                                {londonYearlyPrices.length > 0 ? (
                                    londonYearlyPrices.map(({ year, price }) => (
                                        <div key={year}>
                                            {year}: {price}
                                        </div>
                                    ))
                                ) : (
                                    <div>There is no data for London area.</div>
                                )}
                            </div>
                        </>
                    )}
                </Card>
            </div>
        </>
    )
}
